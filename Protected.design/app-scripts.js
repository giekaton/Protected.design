// Home page component

var data = {
  hash: '',
  message_hex: '',
  message_ascii: '',
  text_hash: 'Please select the file',
  text_file_input: 'Drag and drop or click to select your design file',
  text_input_error: '',
  html_linkhash: 'Please select the file',
  state_hash_calculated: false,
  state_file_selected: false,
  state_dragdropfile: ''
}

Vue.component('app-home', {
  template: '#app-home-template',
  data: function () {
    return data
  },
  methods: {
    faq: function() {
      app.faq = !app.faq;
      window.scrollTo(0, 0);
    },
    verify: function() {
      app.verify = !app.verify;
      app.home = !app.home;
      window.scrollTo(0, 0);
    },
    file_input_click: function() {
      data.state_dragdropfile = false;
      var file_input = document.getElementById('file_input');
      file_input.click();
    },
    file_input_event: function() {
      this.calculate_hash();
      this.state_hash_calculated = true;
      this.state_file_selected = true;
    },
    // @todo allow copyright sign, check other symbols, on input events like paste or mobile input...
    // @idea maybe convert all characters to hex and if result is rectangle, then restrict
    restrict_characters: function() {
      document.getElementById("pd_message").onkeypress = function(e) {
        var chr = String.fromCharCode(e.which);
        if (' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~'.indexOf(chr) < 0) {
          data.text_input_error = 'This character is not supported, please use only ASCII characters.';  
          return false;
        }
        else {
          data.text_input_error = '';
        }
      }
    },
    // Generate protected design page
    generate_pd: function () {
      if (this.state_hash_calculated == true) {
        
        // Update URL
        var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + data.hash.substring(0,12);
        window.history.pushState({path:newurl},'',newurl);

        // Switch components
        app.home = false;
        app.protected_design = true;

        // Source for image preview
        data_protected_design.preview_src = preview_src;

        // Convert message to HEX
        data.message_ascii = document.getElementById("pd_message").value;
        data.message_hex = ascii2hex(data.message_ascii);

        // Update message if status is 'Waiting for payment'
        this.$http.get('wp-json/set_message/set', {params: {message: data.message_hex, hash: data.hash}}).then(response => {
          // success callback
            // Get data for protected design page
            this.$http.get('wp-json/get_protected_design/get', {params: {shortlink: data.hash.substring(0,12)}}).then(response => {
              // success callback
              data_protected_design.message_hex = response.body.message;
              data_protected_design.hash = response.body.hash;
              data_protected_design.text_design_url = home_url + response.body.shortlink;
              data_protected_design.text_status = response.body.status;
              data_protected_design.text_file_size = response.body.file_size + " bytes";
              data_protected_design.text_message_ascii = hex2ascii(data_protected_design.message_hex);
              data_protected_design.text_hash = data_protected_design.hash.substring(0,40);
              data_protected_design.text_tx_hash = response.body.tx_hash;
              data_protected_design.text_tx_url = "https://ropsten.etherscan.io/tx/" + data_protected_design.text_tx_hash;
      
              // Check if design has a preview and if not, then show message
              if (preview_src == "") {
                data_protected_design.preview_image = false;
                data_protected_design.preview_false = true;
                data_protected_design.text_file_input = "Preview is not available for this file format.";
              }
            }, response => {
              // error callback
            });
        }, response => {
          // error callback
        });


      }
      else {
        this.file_input_click();
      }
    },
    calculate_hash: function () {

      var reader = new FileReader(); // Define a Reader
      var filediv = document.getElementById("file_input");
      
      // If file is not drag and dropped
      if(!data.state_dragdropfile) {
        file = filediv.files[0]; // Get the File object
      }

      if (file) {
        // Show the selected file name
        this.text_file_input = "File name: " + file['name'];
        // Change mouse cursor
        document.body.style.cursor = "wait";
        // Change messages
        data.text_hash = "Generating...";
        data.html_linkhash = "Please wait...";

        reader.onload = function (f) {
            var file_result = this.result; // this == reader, get the loaded file "result"
            var file_wordArr = CryptoJS.lib.WordArray.create(file_result); // convert blob to WordArray
            var sha256_hash = CryptoJS.SHA256(file_wordArr); // calculate SHA256 hash
            
            data.hash = sha256_hash.toString();
            data.text_hash = data.hash.substring(0,40);
            data.html_linkhash = "<span class='linkhash'>" + home_url + data.hash.substring(0,12) + "</span>";

            // // Add data to DB using REST API
            // // @todo: protection from spammers
            Vue.http.get('wp-json/new_protected_design/new', {params: {file_size: file['size'], hash: data.hash}}).then(response => {
              // success callback
            }, response => {
              // error callback
            });

            // After calculations are done, change back the mouse cursor
            document.body.style.cursor = "default";
        };
        reader.readAsArrayBuffer(file); //read file as ArrayBuffer

        // Read file as data for visual preview
        if ( (file['type'] == 'image/png') || (file['type'] == 'image/jpeg') || (file['type'] == 'image/gif') ) {
          data_protected_design.preview_false = false;
          data_protected_design.preview_image = true;

          var reader2 = new FileReader();
          var filediv = document.getElementById("file_input");

          reader2.onload = function (f) {
              var file_result = this.result;
              preview_src = f.target.result;
          };
          reader2.readAsDataURL(file);
        }
        else {
          preview_src = "";
          data_protected_design.preview_image = false;
          data_protected_design.preview_false = true;
          data_protected_design.text_file_input = "Preview is not available for this file format.";
        }
      }

    }
  }
})



// Protected design component

var data_protected_design = {
  hash: '',
  message_hex: '',
  text_status: 'Loading...',
  text_hash: 'Loading...',
  text_message_ascii: 'Loading...',
  text_file_size: 'Loading...',
  text_design_url: 'Loading...',
  text_tx_hash: 'Loading...',
  text_tx_url: '',
  text_file_input: '',
  preview_text: false,
  preview_false: false,
  preview_image: false,
  preview_src: '',
  state_dragdropfile: false
}

Vue.component('app-protected-design', {
  template: '#app-protected-design-template',
  data: function () {
    return data_protected_design
  },
  methods: {
    check_status: function() {
      if (data_protected_design.text_status == "Pending") {
        Vue.http.get('wp-json/get_protected_design/get', {params: {shortlink: data_protected_design.hash.substring(0,12)}}).then(response => {
          // success callback
          data_protected_design.text_status = response.body.status;
          if (response.body.status == "Pending") {
            setTimeout(this.check_status, 3000);
          }
          else {
            // Get data for protected design page
            this.$http.get('wp-json/get_protected_design/get', {params: {shortlink: data.hash.substring(0,12)}}).then(response => {
              // success callback
              data_protected_design.message_hex = response.body.message;
              data_protected_design.text_design_url = home_url + response.body.shortlink;
              data_protected_design.text_message_ascii = hex2ascii(data_protected_design.message_hex);
              data_protected_design.text_tx_hash = response.body.tx_hash;
              data_protected_design.text_tx_url = "https://ropsten.etherscan.io/tx/" + data_protected_design.text_tx_hash;
            }, response => {
              // error callback
            });
          }
        }, response => {
          // error callback
        });
      }
    },
    home: function() {
      app.protected_design = !app.protected_design;
      app.home = !app.home;
      pd_false();
      window.scrollTo(0, 0);
    },
    terms: function() {
      app.terms = !app.terms;
      window.scrollTo(0, 0);
    },
    cease: function() {
      app.cease = !app.cease;
      window.scrollTo(0, 0);
    },
    file_input_click: function() {
      var file_input = document.getElementById('file_input');
      file_input.click();
    },
    file_input_event: function() {
      this.text_file_input = "Generating...";
      this.calculate_hash();
      this.state_hash_calculated = true;
    },
    calculate_hash: function () {

      var reader = new FileReader(); //define a Reader
      var filediv = document.getElementById("file_input");
      
      // If file is not drag and dropped
      if(!data_protected_design.state_dragdropfile) {
        console.log("check");
        file = filediv.files[0]; // Get the File object
      }

      if (file) {
        reader.onload = function (f) {

          document.body.style.cursor = "wait";

          var file_result = this.result; // this == reader, get the loaded file "result"
          var file_wordArr = CryptoJS.lib.WordArray.create(file_result); // convert blob to WordArray
          var sha256_hash = CryptoJS.SHA256(file_wordArr); // calculate SHA256 hash
          var calculated_hash = sha256_hash.toString();

          // @todo: Remove legacy substring
          if (calculated_hash.substring(0,12) === data_protected_design.hash.substring(0,12)) {
            
            // Read file as data for visual preview
            var reader2 = new FileReader(); //define a Reader
            var filediv = document.getElementById("file_input");
  
            reader2.onload = function (f) {
                if ( (file['type'] == 'image/png') || (file['type'] == 'image/jpeg') || (file['type'] == 'image/gif') ) {
                var file_result = this.result; // this == reader, get the loaded file "result"
                data_protected_design.preview_src = f.target.result;
                }
                else {
                  data_protected_design.preview_src = "";
                }
                data_protected_design.preview_image = true;
                data_protected_design.preview_text = false;
                data_protected_design.preview_false = false;
            };
            reader2.readAsDataURL(file);
            
          }
          else {
            data_protected_design.preview_src = "";
            data_protected_design.preview_image = false;
            data_protected_design.preview_text = true;
            data_protected_design.text_file_input = "You have selected the wrong file. Please try again.";
          }

          document.body.style.cursor = "default";

        };
        reader.readAsArrayBuffer(file); //read file as ArrayBuffer
      }  
    },
    braintree() {
      var button = document.querySelector('#submit-button');

      braintree.dropin.create({
        authorization: clientToken,
        container: '#dropin-container',
        paypal: {
          flow: 'vault'
        }
      }, function (createErr, instance) {
        if (createErr) {
          // An error in the create call is likely due to
          // incorrect configuration values or network issues.
          // An appropriate error will be shown in the UI.
          console.error(createErr);
          return;
        }

        button.addEventListener('click', function () {
          instance.requestPaymentMethod(function (requestPaymentMethodErr, payload) {

            if (requestPaymentMethodErr) {
              // No payment method is available.
              // An appropriate error will be shown in the UI.
              console.error(requestPaymentMethodErr);
              return;
            }
            else {
              button.disabled = true;
              button.innerHTML = "Please wait...";
              button.style.backgroundColor = "#c9c9c9";
              button.style.color = "#212121";
              button.style.cursor = "default";
            }

            // Submit payload.nonce to server and update UI
            Vue.http.get('wp-json/braintree/nonce', {params: {nonce: payload.nonce, hash: data_protected_design.hash}}).then(response => {
              
              if (response.body.success == true) {

                // If message provided, add it to the end of hash
                if (data_protected_design.message_hex != null) {
                  submit_hash_message = data_protected_design.hash.substring(0,40) + data_protected_design.message_hex;
                }
                else {
                  submit_hash_message = data_protected_design.hash.substring(0,40);
                }

                // Submit tx
                Vue.http.get('wp-json/submit_tx/submit', {params: {hash: submit_hash_message}}).then(response => {
                  // success callback
                }, response => {
                  // error callback
                });

                data_protected_design.text_status = "Pending";

                // Get info from db
                Vue.http.get('wp-json/get_protected_design/get', {params: {shortlink: data_protected_design.hash.substring(0,12)}}).then(response => {                
                  data_protected_design.text_status = response.body.status;
                  data_protected_design.text_tx_hash = response.body.tx_hash;
                  data_protected_design.text_tx_url = "https://ropsten.etherscan.io/tx/" + data_protected_design.text_tx_hash;
                }, response => {
                  // error callback
                });

              }

            }, response => {
              // error callback
            });

          });
        });
      });
    }
  },
  mounted: function () {
    this.braintree();
  }
})




// Verify component

var data_verify = {
  hash: '',
  state_hash_calculated: false,
  text_file_input: 'Drag and drop or click to select your design file to verify it',
  text_verify_result: '',
  text_verify_link: '',
  state_dragdropfile: ''
}

Vue.component('app-verify', {
  data: function () {
    return data_verify
  },
  methods: {
    verify: function() {
      app.verify = !app.verify;
      app.home = !app.home;
      window.scrollTo(0, 0);

      data_verify.state_hash_calculated = false;
      data_verify.text_file_input = 'Drag and drop or click to select your design file to verify it';
      data_verify.text_verify_result = '';
      data_verify.text_verify_link = '';
    },
    file_input_click: function() {
      var file_input = document.getElementById('file_input');
      file_input.click();
    },
    file_input_event: function() {
      this.calculate_hash();
      this.state_hash_calculated = true;
    },
    calculate_hash: function () {
      var reader = new FileReader(); //define a Reader
      var filediv = document.getElementById("file_input");
    

      // If file is not drag and dropped
      if(!this.state_dragdropfile) {
        file = filediv.files[0]; // Get the File object
      }

      if (file) {
        this.text_file_input = "File name: " + file['name'];
        this.text_verify_result = 'Generating...';
        data_verify.text_verify_link = '';
        document.body.style.cursor = "wait";

        reader.onload = function (f) {
          
            var file_result = this.result; // this == reader, get the loaded file "result"
            var file_wordArr = CryptoJS.lib.WordArray.create(file_result); // convert blob to WordArray
            var sha256_hash = CryptoJS.SHA256(file_wordArr); // calculate SHA256 hash
            data_verify.hash = sha256_hash.toString();

            // Update message if status is 'Waiting for payment'
            Vue.http.get('wp-json/get_protected_design/get', {params: {shortlink: data_verify.hash.substring(0,12)}}).then(response => {
              // success callback
              if (response.body != null) {
                if (response.body.hash.substring(0,12) == data_verify.hash.substring(0,12)) {
                  data_verify.text_verify_result = "This file is submitted to Protected.design. To see its status, please visit the following link:";
                  data_verify.text_verify_link = home_url + data_verify.hash.substring(0,12);
                }
              }
              else {
                data_verify.text_verify_result = "This file is not submitted to Protected.design";
                data_verify.text_verify_link = "";
              }
            }, response => {
              // error callback
            });

            document.body.style.cursor = "default";
        };
        reader.readAsArrayBuffer(file); // read file as ArrayBuffer

        // Read file as data for visual preview
        if ( (file['type'] == 'image/png') || (file['type'] == 'image/jpeg') || (file['type'] == 'image/gif') ) {
          data_protected_design.preview_false = false;
          data_protected_design.preview_image = true;
          data_protected_design.preview_text = false;

          var reader2 = new FileReader();
          var filediv = document.getElementById("file_input");

          reader2.onload = function (f) {
              var file_result = this.result;
              preview_src = f.target.result;
          };
          reader2.readAsDataURL(file);
        }
        else {
          preview_src = "";
          data_protected_design.preview_false = true;
          data_protected_design.text_file_input = "Drag and drop or click to select your file to verify it. Preview is not available for this file format.";
        }
        
      }
    },
    generate_pd: function () {
      if (this.state_hash_calculated == true) {
        
        // Update URL
        var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + data_verify.hash.substring(0,12);
        window.history.pushState({path:newurl},'',newurl);

        // Switch components
        app.verify = false;
        app.protected_design = true;

        // Source for image preview
        data_protected_design.preview_src = preview_src;

        // Get data for protected design page
        Vue.http.get('wp-json/get_protected_design/get', {params: {shortlink: data_verify.hash.substring(0,12)}}).then(response => {
          // success callback
          data_protected_design.message_hex = response.body.message;
          data_protected_design.hash = response.body.hash;
          data_protected_design.text_design_url = home_url + response.body.shortlink;
          data_protected_design.text_status = response.body.status;
          data_protected_design.text_file_size = response.body.file_size + " bytes";
          data_protected_design.text_message_ascii = hex2ascii(data_protected_design.message_hex);
          data_protected_design.text_hash = data_protected_design.hash.substring(0,40);
          data_protected_design.text_tx_hash = response.body.tx_hash;
          data_protected_design.text_tx_url = "https://ropsten.etherscan.io/tx/" + data_protected_design.text_tx_hash;
        }, response => {
          // error callback
        });

        // Check if design has a preview and if not, then show message
        if (preview_src == "") {
          data_protected_design.preview_image = false;
          data_protected_design.preview_text = false;
          data_protected_design.preview_false = true;
          data_protected_design.text_file_input = "Preview is not available for this file format.";
        }
      }
      else {
        this.file_input_click();
      }
    }
  },
  template: '#app-verify-template'
});



// FAQ component

Vue.component('app-faq', {
  methods: {
    faq: function() {
      app.faq = !app.faq;
      window.scrollTo(0, 0);
    }
  },
  template: '#app-faq-template'
});



// Cease and desist letter component

Vue.component('app-cease', {
  methods: {
    cease: function() {
      app.cease = !app.cease;
      window.scrollTo(0, 0);
    }
  },
  template: '#app-cease-template'
});



// Terms and conditions component

Vue.component('app-terms', {
  methods: {
    terms: function() {
      app.terms = !app.terms;
      window.scrollTo(0, 0);
    }
  },
  template: '#app-terms-template'
});



// Footer component

Vue.component('app-footer', {
  template: '#app-footer-template'
});



// Main Vue instance

var app = new Vue({
  el: "#app",
  data: {
    home: true,
    protected_design: false,
    faq: false,
    terms: false,
    cease: false,
    verify: false
  }
})



// Global JS

function pd_false() {
  window.history.pushState('', '', home_url);
  data.state_hash_calculated = false;
  data.state_file_selected = false;
  data.text_file_input = 'Drag and drop or click to select your design file';

  data_protected_design.hash = '';
  data_protected_design.message_hex = '';
  data_protected_design.text_status = 'Loading...';
  data_protected_design.text_hash = 'Loading...';
  data_protected_design.text_message_ascii = 'Loading...';
  data_protected_design.text_file_size = 'Loading...';
  data_protected_design.text_design_url = 'Loading...';
  data_protected_design.text_tx_hash = 'Loading...';
  data_protected_design.text_tx_url = '';
  data_protected_design.text_file_input = '';
  data_protected_design.preview_text = false;
  data_protected_design.preview_false = false;
  data_protected_design.preview_image = false;
  data_protected_design.preview_src = '';
  data_protected_design.state_dragdropfile = false;
}

window.onpopstate = function() {
  app.home = true;
  app.protected_design = false;
}

// Status messages
// function Status(status_code) {
//   switch (status_code) {
//   case 1:
//     data_protected_design.status = "Waiting for payment";
//     break;
//   case 2:
//     data_protected_design.status = "Pending";
//     break;
//   case 3:
//     data_protected_design.status = "Protected";
//     break;    
//   }
// }



//
// Drag and drop handlers
//
function dragOverHandler(ev) {
  // console.log('File(s) in drop zone'); 
  document.getElementById("dragdropcontainer").style.backgroundColor = "white";
  // Prevent default behavior (Prevent file from being opened)
  ev.preventDefault();
}

function dragLeaveHandler(ev) {
  // console.log('File(s) in drop zone'); 
  document.getElementById("dragdropcontainer").style.backgroundColor = "#e6e6e6";
  // Prevent default behavior (Prevent file from being opened)
  ev.preventDefault();
}

function dropHandler(ev) {
  // console.log('File(s) dropped');
  document.getElementById("dragdropcontainer").style.backgroundColor = "#e6e6e6";

  // Prevent default behavior (Prevent file from being opened)
  ev.preventDefault();

  if (ev.dataTransfer.items) {
    // Use DataTransferItemList interface to access the file(s)
    for (var i = 0; i < ev.dataTransfer.items.length; i++) {
      // If dropped items aren't files, reject them
      if (ev.dataTransfer.items[i].kind === 'file') {
        file = ev.dataTransfer.items[i].getAsFile();
        if(app.home) {
          data.state_dragdropfile = true;
          app.$refs.app_home.file_input_event();
        }
        if(app.verify) {
          data_verify.state_dragdropfile = true;
          app.$refs.app_verify.file_input_event();
        }
        if(app.protected_design) {
          data_protected_design.state_dragdropfile = true;
          app.$refs.app_protected_design.file_input_event();
        }
      }
    }
  } else {
    // Use DataTransfer interface to access the file(s)
    for (var i = 0; i < ev.dataTransfer.files.length; i++) {
      file = ev.dataTransfer.items[i].getAsFile();
      if(app.home) {
        data.state_dragdropfile = true;
        app.$refs.app_home.file_input_event();
      }
      if(app.verify) {
        data_verify.state_dragdropfile = true;
        app.$refs.app_verify.file_input_event();
      }
      if(app.protected_design) {
        data_protected_design.state_dragdropfile = true;
        app.$refs.app_protected_design.file_input_event();
      }
    }
  } 
  
  // Pass event to removeDragData for cleanup
  removeDragData(ev)
}

function removeDragData(ev) {
  console.log('Removing drag data')

  if (ev.dataTransfer.items) {
    // Use DataTransferItemList interface to remove the drag data
    ev.dataTransfer.items.clear();
  } else {
    // Use DataTransfer interface to remove the drag data
    ev.dataTransfer.clearData();
  }
}



// Convert HEX - ASCII - HEX

function ascii2hex(str) {
  var arr = [];
  for (var i = 0, l = str.length; i < l; i ++) {
    var hex = Number(str.charCodeAt(i)).toString(16);
    arr.push(hex);
  }
  return arr.join('');
}

function hex2ascii(hexx) {
  var hex = hexx.toString();//force conversion
  var str = '';
  for (var i = 0; i < hex.length; i += 2)
      str += String.fromCharCode(parseInt(hex.substr(i, 2), 16));
  return str;
}