// Header component

var data_header = {

}

Vue.component('app-header', {
  data: function () {
    return data_header
  },
  methods: {
    terms: function() {
      app.location = 'terms';
      pd_false();
      window.scrollTo(0, 0);
    },
    faq: function() {
      app.location = 'faq';
      window.scrollTo(0, 0);
      // Update URL
      var stateObj = { location: app.location };
      history.pushState(stateObj, "", app.location);
    },
    examples: function() {
      app.location = 'examples';
      window.scrollTo(0, 0);
      // Update URL
      var stateObj = { location: app.location };
      history.pushState(stateObj, "", app.location);
    },
    home: function() {
      app.location = 'home';
      pd_false();
      window.scrollTo(0, 0);

      // Update URL
      var stateObj = { location: "/" };
      history.pushState(stateObj, "", "/");
    },
    verify: function() {
      app.location = 'verify';
      pd_false();
      window.scrollTo(0, 0);
      
      data_verify.hash = '';
      data_verify.state_hash_calculated = false;
      data_verify.text_file_input = 'Drag and drop or click to select your design file to verify it';
      data_verify.text_verify_result = '';
      data_verify.text_verify_link = '';
      data_verify.state_dragdropfile = '';

      // Update URL
      var stateObj = { location: app.location };
      history.pushState(stateObj, "", app.location);
    }
  },
  template: '#app-header-template'
});



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
      pd_false();
      app.faq = !app.faq;
      window.scrollTo(0, 0);

      // Update URL
      var stateObj = { location: app.location };
      history.pushState(stateObj, "", app.location);
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
        window.scrollTo(0, 0);
        
        // Update URL
        var stateObj = { location: data.hash.substring(0,12) };
        history.pushState(stateObj, "", data.hash.substring(0,12));

        // Switch components
        app.location = 'protected_design';

        // Source for image preview
        data_protected_design.preview_src = preview_src;

        // Convert message to HEX
        data.message_ascii = document.getElementById("pd_message").value;
        data.message_hex = ascii2hex(data.message_ascii);

        // Get preview_url
        data.preview_url = document.getElementById("pd_preview_url").value;

        // Update message if status is 'Ready for protection'
        this.$http.get('wp-json/set_message/set', {params: {message: data.message_hex, preview_url: data.preview_url, hash: data.hash}}).then(response => {
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
              data_protected_design.text_hash = data_protected_design.hash;
              data_protected_design.text_tx_hash = response.body.tx_hash;
              data_protected_design.text_tx_url = etherscan_url + "/tx/" + data_protected_design.text_tx_hash;
              data_protected_design.protection_type = response.body.protection_type;
              data_protected_design.html_grouped_file = '<a href="https://protected.design/files/' + response.body.grouped_hash + '.txt" target="_blank">' +  response.body.grouped_hash + '.txt</a>';
              data_protected_design.text_grouped_file = response.body.grouped_hash + ".txt";
              if (response.body.tx_timestamp.length > 1) {
                data_protected_design.text_tx_timestamp = response.body.tx_timestamp;
              }
      
              // Check if design has a preview and if not, then show message
              if (preview_src == "") {
                data_protected_design.preview_image = false;
                data_protected_design.preview_false = true;
                data_protected_design.text_file_input = "Preview is not available for this file format";
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
            data.text_hash = data.hash;
            data.html_linkhash = "<div style='margin-top:6px;'><span style='padding-top:2px;padding-bottom:3px;' class='linkhash'>" + home_url + data.hash.substring(0,12) + "</span></div><div style='display:block;margin-top:9px;height:20px;cursor:pointer;'><img src='/img/arrow-up-3.svg' width='25'></div>";

            // // Add data to DB using REST API
            // // @todo: protection from spammers
            Vue.http.get('wp-json/new_protected_design/new', {params: {file_size: file['size'], hash: data.hash}}).then(response => {
              // success callback
            }, response => {
              // error callback
            });

            // After calculations are done, change back the mouse cursor
            document.body.style.cursor = "default";

            visual_reader(data.text_hash);
        };
        reader.readAsArrayBuffer(file); //read file as ArrayBuffer


        function visual_reader(hash) {
          // Read file as data for visual preview
          if ( (file['type'] == 'image/png') || (file['type'] == 'image/jpeg') || (file['type'] == 'image/gif') ) {
            data_protected_design.preview_false = false;
            data_protected_design.preview_image = true;

            var reader2 = new FileReader();
            var filediv = document.getElementById("file_input");

            reader2.onload = function (f) {
                var file_result = this.result;
                preview_src = f.target.result;
                sessionStorage.setItem('session_preview_src_'+hash, preview_src);
            };
            reader2.readAsDataURL(file);
          }
          else {
            preview_src = "";
            data_protected_design.preview_image = false;
            data_protected_design.preview_false = true;
            data_protected_design.text_file_input = "Preview is not available for this file format";
          }
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
  text_tx_timestamp: 'Please wait...',
  text_tx_url: '',
  text_donations_address: donations_address,
  text_file_input: '',
  preview_text: false,
  preview_false: false,
  preview_image: false,
  preview_src: '',
  state_dragdropfile: false,
  loader: false,
  class_preview_image: 'preview-image', 
  class_preview_image_loader: 'preview-image-loader',
  protection_type: '', // individual=1, grouped=2
  html_grouped_file: '',
  text_grouped_file: ''
}

Vue.component('app-protected-design', {
  template: '#app-protected-design-template',
  data: function () {
    return data_protected_design
  },
  methods: {
    check_status: function() {
      if (data_protected_design.text_status == "Pending" || data_protected_design.text_status == "Payment received") {
        Vue.http.get('wp-json/get_protected_design/get', {params: {shortlink: data_protected_design.hash.substring(0,12)}}).then(response => {
          // success callback
          data_protected_design.text_status = response.body.status;
          if (response.body.status == "Pending" || data_protected_design.text_status == "Payment received") {
            setTimeout(this.check_status, 3000);
          }
          else {
            // Get data for protected design page
            this.$http.get('wp-json/get_protected_design/get', {params: {shortlink: data_protected_design.hash.substring(0,12)}}).then(response => {
              // success callback
              data_protected_design.message_hex = response.body.message;
              data_protected_design.text_design_url = home_url + response.body.shortlink;
              data_protected_design.text_message_ascii = hex2ascii(data_protected_design.message_hex);
              data_protected_design.text_tx_hash = response.body.tx_hash;
              data_protected_design.text_tx_url = etherscan_url + "/tx/" + data_protected_design.text_tx_hash;
            }, response => {
              // error callback
            });

            setTimeout(this.check_tx_timestamp, 3000);
          }
        }, response => {
          // error callback
        });
      }
    },
    check_tx_timestamp: function() {
      if (data_protected_design.text_status == "Protected" && data_protected_design.text_tx_timestamp == "Please wait...") {
        Vue.http.get('wp-json/get_protected_design/get', {params: {shortlink: data_protected_design.hash.substring(0,12)}}).then(response => {
          // success callback
          if (response.body.tx_timestamp == "") {
            setTimeout(this.check_tx_timestamp, 3000);
          }
          else {
            data_protected_design.text_tx_timestamp = response.body.tx_timestamp;
          }
        }, response => {
          // error callback
        });
      }
    },
    terms_pd: function() {
      app.terms_pd = !app.terms_pd;
      window.scrollTo(0, 0);
      if (app.terms_pd) {
        document.getElementById("body-overlay").style.display = "block";
      }
      else if (!app.terms_pd) {
        document.getElementById("body-overlay").style.display = "none";
      }
    },
    home: function() {
      app.location = 'home';
      pd_false();
      window.scrollTo(0, 0);

      // Update URL
      var stateObj = { location:"" };
      history.pushState(stateObj, "", "");
    },
    cease: function() {
      app.cease = !app.cease;
      if (app.cease) {
        document.getElementById("body-overlay").style.display = "block";
      }
      else if (!app.cease) {
        document.getElementById("body-overlay").style.display = "none";
      }
      window.scrollTo(0, 0);
    },
    certificate: function() {
      data_certificate.preview_src = data_protected_design.preview_src;
      app.certificate = !app.certificate;
      if (app.certificate) {
        document.getElementById("body-overlay").style.display = "block";
      }
      else if (!app.certificate) {
        document.getElementById("body-overlay").style.display = "none";
      }
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
            data_protected_design.text_file_input = "You have selected the wrong file, please try again";
          }

          document.body.style.cursor = "default";

        };
        reader.readAsArrayBuffer(file); //read file as ArrayBuffer
      }  
    },
    cardinity: function() {
      // Get user's country code
      Vue.http.get('https://extreme-ip-lookup.com/json/').then(response => {
        countryCode = response.body.countryCode;
        // console.log('User\'s Country:', countryCode);
      }, response => {
        countryCode = "US";
      });

      // Initialize CC validation
      var creditly = Creditly.initialize(
        '.creditly-wrapper .expiration-month-and-year',
        '.creditly-wrapper .credit-card-number',
        '.creditly-wrapper .security-code',
        '.creditly-wrapper .card-type');

      var button = document.querySelector('#submit-button');

      button.addEventListener('click', function(e) {
        
        if(document.getElementById('agree').checked) {
          document.getElementById('ccError').innerHTML = '';
          document.getElementById('cvvError').innerHTML = '';
          document.getElementById('expError').innerHTML = '';
          document.getElementById('holderError').innerHTML = '';
          document.getElementById('paymentError').innerHTML = '';
          // document.getElementById('cardHolder').classList.remove('has-error');

          // Error messages
          document.body.addEventListener("creditly_client_validation_error", function(e) {
            for (var msg in e.data.messages) {
              if (e.data.messages[msg] == "Your credit card number is invalid") {
                document.getElementById('ccError').innerHTML = e.data.messages[msg];
              }
              if (e.data.messages[msg] == "Your security code is invalid") {
                document.getElementById('cvvError').innerHTML = e.data.messages[msg];
              }
              if (e.data.messages[msg] == "Your credit card expiration is invalid") {
                document.getElementById('expError').innerHTML = e.data.messages[msg];
              }
            }
          }, false);
          cardHolder = document.getElementById('cardHolder');
          if (cardHolder.value.length < 2) {
            cardHolder.classList.add('has-error');
            document.getElementById('holderError').innerHTML = "Your credit card name is invalid";
            holderErr = true;
          }
          else {
            holderErr = false;
          }

          e.preventDefault();
          var output = creditly.validate();
          if (output && !holderErr) {
            // Do something with your credit card output.
            var cardNr = output["number"];
            var cardCVC = output["security_code"];
            var cardExpMonth = output["expiration_month"];
            var cardExpYear = output["expiration_year"];
            var cardHolder = document.getElementById('cardHolder').value;
            var hashTrunc = data_protected_design.hash.substring(0,12);
            // console.log(cardNr, cardCVC, cardExpMonth, cardExpYear, cardHolder, hashTrunc, countryCode);
          
          
            button.disabled = true;
            button.innerHTML = "Please wait...";
            button.style.backgroundColor = "#c9c9c9";
            button.style.color = "#212121";
            button.style.cursor = "default";


            // Submit payload.nonce to server and update UI
            Vue.http.get('wp-json/cardinity/set', {params: {
            cardNr: cardNr, 
            cardCVC: cardCVC, 
            cardExpMonth: cardExpMonth, 
            cardExpYear: cardExpYear, 
            cardHolder: cardHolder, 
            hashTrunc: hashTrunc, 
            countryCode: countryCode,}}).then(response => {
              
              if (response.body == 'approved') {

                // If message provided, add it to the end of hash
                if (data_protected_design.message_hex != null) {
                  submit_hash_message = data_protected_design.hash + data_protected_design.message_hex;
                }
                else {
                  submit_hash_message = data_protected_design.hash;
                }

                // Create transaction hex
                Vue.http.get('wp-json/submit_tx/submit', {params: {hash: submit_hash_message}}).then(response => {
                  // success callback
                }, response => {
                  // error callback
                });

                // Change status and content on front end
                data_protected_design.text_status = "Pending";

                // Get info from db, update front end
                Vue.http.get('wp-json/get_protected_design/get', {params: {shortlink: data_protected_design.hash.substring(0,12)}}).then(response => {                
                  data_protected_design.text_status = response.body.status;
                  data_protected_design.text_tx_hash = response.body.tx_hash;
                  data_protected_design.text_tx_url = etherscan_url + "/tx/" + data_protected_design.text_tx_hash;
                  
                }, response => {
                  // error callback
                });

              }

              else if (response.body.ThreeDForm) {
                console.log ("Pending");

                function redirectPost(url) {
                  var form = document.createElement('form');
                  document.body.appendChild(form);
                  form.method = 'post';
                  form.action = url;
                  for (var name in data) {
                      var input = document.createElement('input');
                      input.type = 'hidden';
                      input.name = 'PaReq';
                      input.value = response.body.PaReq;
                      form.appendChild(input);
                  }
                  for (var name in data) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'TermUrl';
                    input.value = 'https://protected.design/wp-content/themes/protected.design/cardinity_callback.php';
                    form.appendChild(input);
                  }
                  for (var name in data) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'MD';
                    input.value = response.body.MD;
                    form.appendChild(input);
                  }
                  form.submit();
                }
                
                redirectPost(response.body.ThreeDForm);

              }

              else {
                // console.log(response.body);

                document.getElementById('paymentError').innerHTML = "Your payment was declined. You can try again, please double check the data you have entered.";

                button.disabled = false;
                button.innerHTML = "Protect this design";
                button.style.backgroundColor = "#ffcc00";
                button.style.color = "black";
                button.style.cursor = "pointer";
              }
            }, response => {
              // error callback
            });
          
          }
          
        }
        else {
          alert('Please read and agree to the Terms and Conditions'); return false;
        }
      });
    },
    protection_2() {
      var button2 = document.querySelector('#submit-button-2');

      button2.addEventListener('click', function () {
        if(document.getElementById('agree-2').checked) { 

          // Change status and content on front end
          data_protected_design.text_status = "Scheduled";

          // If message provided, add it to the end of hash
          if (data_protected_design.message_hex != null) {
            submit_hash_message = data_protected_design.hash + data_protected_design.message_hex;
          }
          else {
            submit_hash_message = data_protected_design.hash;
          }

          // Create transaction hex
          Vue.http.get('wp-json/submit_tx_2/submit', {params: {hash: submit_hash_message}}).then(response => {
            // success callback
          }, response => {
            // error callback
          });

        } 
          
        else {
          alert('Please read and agree to the Terms and Conditions'); return false;
        }

      });

    }
  },
  mounted: function () {
    this.cardinity();
    this.protection_2();
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

            // Update message if status is 'Ready for protection'
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
          data_protected_design.text_file_input = "Drag and drop or click to select your file to verify it";
        }
        
      }
    },
    generate_pd: function () {
      if (this.state_hash_calculated == true) {
        
        // Update URL
        var stateObj = { location: data.hash.substring(0,12) };
        history.pushState(stateObj, "", data.hash.substring(0,12));

        // Switch components
        app.location = "protected_design";

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
          data_protected_design.text_hash = data_protected_design.hash;
          data_protected_design.text_tx_hash = response.body.tx_hash;
          data_protected_design.text_tx_timestamp = response.body.tx_timestamp;
          data_protected_design.text_tx_url = etherscan_url + "/tx/" + data_protected_design.text_tx_hash;
          data_protected_design.protection_type = response.body.protection_type;
          data_protected_design.html_grouped_file = '<a href="https://protected.design/files/' + response.body.grouped_hash + '.txt" target="_blank">' +  response.body.grouped_hash + '.txt</a>';
          data_protected_design.text_grouped_file = response.body.grouped_hash + ".txt";
        }, response => {
          // error callback
        });

        // Check if design has a preview and if not, then show message
        if (preview_src == "") {
          data_protected_design.preview_image = false;
          data_protected_design.preview_text = false;
          data_protected_design.preview_false = true;
          data_protected_design.text_file_input = "Preview is not available for this file format";
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
  template: '#app-faq-template'
});



// Certificate component

var data_certificate = {
  savePdfButton: false,
  preview_cors_message: '',
  previewCors: false,
  preview_src: '',
  previewCorsPassed: false
}

Vue.component('app-certificate', {
  data: function () {
    return data_certificate
  },
  methods: {
    calculateHashCert: function () {
      var file_input = document.getElementById('file_input_cert');
      file_input.click();

      var reader = new FileReader(); //define a Reader
      var filediv = document.getElementById("file_input_cert");
      var file = filediv.files[0];

      if (file) {
        reader.onload = function (f) {
          
          document.body.style.cursor = "wait";

          var file_result = this.result; // this == reader, get the loaded file "result"
          var file_wordArr = CryptoJS.lib.WordArray.create(file_result); // convert blob to WordArray
          var sha256_hash = CryptoJS.SHA256(file_wordArr); // calculate SHA256 hash
          var calculated_hash = sha256_hash.toString();

          if (calculated_hash.substring(0,12) === data_protected_design.hash.substring(0,12)) {
            
            // Read file as data for visual preview
            var reader2 = new FileReader(); //define a Reader
            var filediv = document.getElementById("file_input_cert");
  
            reader2.onload = function (f) {
                if ( (file['type'] == 'image/png') || (file['type'] == 'image/jpeg') || (file['type'] == 'image/gif') ) {
                var file_result = this.result; // this == reader, get the loaded file "result"
                data_certificate.preview_src = f.target.result;
                data_certificate.previewCors = false;
                data_certificate.previewCorsPassed = true;
                data_certificate.preview_cors_message = '';
                }
                else {
                  data_certificate.preview_src = "";
                }

            };
            reader2.readAsDataURL(file);
            
          }
          else {
            data_certificate.preview_src = "";        
            data_certificate.preview_cors_message = '<span style="color:red;">You have selected the wrong file, please try again.</span><br><br>';
      
          }

          document.body.style.cursor = "default";

        };
        reader.readAsArrayBuffer(file); //read file as ArrayBuffer
      }  
    },
    file_input_event_cert: function() {
      this.calculateHashCert();
    },
    certificate: function() {
      app.certificate = !app.certificate;
      if (app.certificate) {
        document.getElementById("body-overlay").style.display = "block";
      }
      else if (!app.certificate) {
        document.getElementById("body-overlay").style.display = "none";
      }
      window.scrollTo(0, 0);
      data_certificate.savePdfButton = false;
      data_certificate.preview_cors_message = '';
      data_certificate.previewCors = false;
      data_certificate.previewCors = false;
      data_certificate.previewCorsPassed = false;
    },
    savePdf: function(pdfContinue) {
      data_certificate.savePdfButton = null;

      if ((data_protected_design.preview_src.substring(0,4) == "http") && (pdfContinue != true)) {
        window.scrollTo(0, 0);
        data_certificate.previewCors = true;
        data_certificate.preview_cors_message = '<span style="color:red;">Image preview will not be rendered in the PDF because it is loaded from the remote url. Please select the image file manually from your local disk, or continue without the preview.</span><br><br>';
      }
      else {
        var element = document.getElementById('cert-to-pdf');

        html2pdf(element, {
          margin:       1,
          filename:     'Protected.design.' + data_protected_design.text_hash.substring(0,12) + '.pdf',
          image:        { type: 'jpeg', quality: 0.98 },
          html2canvas:  { dpi: 192, letterRendering: true },
          jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
        });
      }
    }
  },
  created() {
    if (!html2pdfScript) {
      var html2pdfScript = document.createElement('script');

      if(html2pdfScript.readyState) { // IE
        html2pdfScript.onreadystatechange = function() {
          if ( html2pdfScript.readyState === "loaded" || html2pdfScript.readyState === "complete" ) {
            html2pdfScript.onreadystatechange = null;
            data_certificate.savePdfButton = true;
          }
        };
      } else { // Others
        html2pdfScript.onload = function() {
          data_certificate.savePdfButton = true;
        };
      }
    }
    else {
      data_certificate.savePdfButton = true;
    }

    html2pdfScript.setAttribute('src', 'https://protected.design/wp-content/themes/protected.design/includes/html2pdf.bundle.min.js');
    document.head.appendChild(html2pdfScript);

  },
  template: '#app-certificate-template'
});



var data_cease = {
  txt_country: '',
  txt_date: '',
  txt_name: '',
  txt_ownersname: '',
  txt_designtype: '',
  txt_designname: '',
  state_letter_generated: false
}

// Cease and desist letter component

Vue.component('app-cease', {
  data: function () {
    return data_cease
  },
  methods: {
    cease: function() {
      app.cease = !app.cease;
      if (app.cease) {
        document.getElementById("body-overlay").style.display = "block";
      }
      else if (!app.cease) {
        document.getElementById("body-overlay").style.display = "none";
      }
      window.scrollTo(0, 0);
    }
  },
  template: '#app-cease-template'
});


// Terms and Conditions modal component

Vue.component('app-terms-pd', {
  methods: {
    terms_pd: function() {
      app.terms_pd = !app.terms_pd;
      window.scrollTo(0, 0);
      if (app.terms_pd) {
        document.getElementById("body-overlay").style.display = "block";
      }
      else if (!app.terms_pd) {
        document.getElementById("body-overlay").style.display = "none";
      }
    }
  },
  template: '#app-terms-pd-template'
});



// Examples component

Vue.component('app-examples', {
  methods: {
    examples_link: function(shortlink) {
      pd_false();

      // Update URL
      var stateObj = { location: shortlink };
      history.pushState(stateObj, "", shortlink);

      // Switch components
      app.location = 'protected_design';

      // Get data for protected design page
      this.$http.get('wp-json/get_protected_design/get', {params: {shortlink: shortlink}}).then(response => {
        // success callback
        if (!response.body){
          app.location = "404";
          return;
        }
        data_protected_design.message_hex = response.body.message;
        data_protected_design.hash = response.body.hash;
        data_protected_design.text_design_url = home_url + response.body.shortlink;
        data_protected_design.text_status = response.body.status;
        data_protected_design.text_file_size = response.body.file_size + " bytes";
        data_protected_design.text_message_ascii = hex2ascii(data_protected_design.message_hex);
        data_protected_design.text_hash = data_protected_design.hash;
        data_protected_design.text_tx_hash = response.body.tx_hash;
        data_protected_design.text_tx_url = etherscan_url + "/tx/" + data_protected_design.text_tx_hash;
        data_protected_design.protection_type = response.body.protection_type;
        data_protected_design.html_grouped_file = '<a href="https://protected.design/files/' + response.body.grouped_hash + '.txt" target="_blank">' +  response.body.grouped_hash + '.txt</a>';
        data_protected_design.text_grouped_file = response.body.grouped_hash + ".txt";
        if (response.body.tx_timestamp.length > 1) {
          data_protected_design.text_tx_timestamp = response.body.tx_timestamp;
        }

        // If preview URL provided by the user, generate hash on the backend to compare it with the protected design hash
        var preview_url = response.body.preview_url;
        if (preview_url) {
            if ((preview_url.endsWith('png')) || (preview_url.endsWith('jpg')) || (preview_url.endsWith('jpeg')) || (preview_url.endsWith('gif'))) {
                data_protected_design.loader = true;

                Vue.http.get('wp-json/php_hash/get', {params: {preview_src: preview_url}}).then(response => {
                // success callback
                // console.log("1" + data_protected_design.hash.substring(0,12));
                // console.log("1" + response.body.substring(0,12));
                if (response.body.substring(0,12) == data_protected_design.hash.substring(0,12)) {
                    data_protected_design.preview_text = false;
                    data_protected_design.preview_image = true;
                    data_protected_design.preview_src = preview_url;
                }
                else if (response.body.substring(0,12) == 'error_size') {
                  data_protected_design.preview_text = true;
                  data_protected_design.text_file_input = 'The preview file exceeds 15 MB. Select your file to to verify and generate its preview.'
                }
                else {
                    data_protected_design.preview_text = true;
                    data_protected_design.text_file_input = 'The preview file does not belong to this protected design. Select your file to to verify and generate its preview.'
                }
                }, response => {
                // error callback
                });
            }
            else { 
                data_protected_design.preview_text = true;
                if (response.body.status == "Protected") {
                    data_protected_design.text_file_input = 'This design is protected<div style="margin-top:2px;font-size:15px;">Drag and drop or click to select your design file to verify it and generate its preview</div>';
                }
                else {
                    data_protected_design.text_file_input = 'Drag and drop or click to select your design file to verify it and generate its preview';
                }
            }
        }
        else {
          data_protected_design.preview_text = true;
          if (response.body.status == "Protected") {
              data_protected_design.text_file_input = 'This design is protected<div style="margin-top:2px;font-size:15px;">Drag and drop or click to select your design file to verify it and generate its preview</div>';
          }
          else {
              data_protected_design.text_file_input = 'Drag and drop or click to select your design file to verify it and generate its preview';
          }
        }
      }, response => {
        // error callback
      });
      
    }
  },
  template: '#app-examples-template'
});



// Terms and Conditions component

Vue.component('app-terms', {
  template: '#app-terms-template'
});


// 404 component

Vue.component('app-404', {
  template: '#app-404-template'
});


// Privacy Policy component

Vue.component('app-privacy', {
  template: '#app-privacy-template'
});



// Contacts component

Vue.component('app-contacts', {
  template: '#app-contacts-template'
});



// Footer component

Vue.component('app-footer', {
  methods: {
    terms: function() {
      pd_false();
      app.location = 'terms';
      window.scrollTo(0, 0);

      // Update URL
      var stateObj = { location: app.location };
      history.pushState(stateObj, "", app.location);
    },
    faq: function() {
      pd_false();
      app.location = 'faq';
      window.scrollTo(0, 0);

      // Update URL
      var stateObj = { location: app.location };
      history.pushState(stateObj, "", app.location);
    },
    privacy: function() {
      pd_false();
      app.location = 'privacy';
      window.scrollTo(0, 0);

      // Update URL
      var stateObj = { location: app.location };
      history.pushState(stateObj, "", app.location);
    },
    contacts: function() {
      pd_false();
      app.location = 'contacts';
      window.scrollTo(0, 0);

      // Update URL
      var stateObj = { location: app.location };
      history.pushState(stateObj, "", app.location);
    }
  },
  template: '#app-footer-template'
});



// Main Vue instance

var app = new Vue({
  el: "#app",
  data: {
    location: 'home',
    home: true,
    protected_design: false,
    faq: false,
    terms: false,
    terms_pd: false,
    privacy: false,
    contacts: false,
    certificate: false,
    cease: false,
    verify: false
  },
  beforeMount: function () {
    document.getElementById("pre-load").style.display = "none";
  }
})



// Global JS

function pd_false() {
  // window.history.pushState('', '', home_url);
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
  data_protected_design.text_tx_timestamp = 'Please wait...';  
  data_protected_design.preview_text = false;
  data_protected_design.preview_false = false;
  data_protected_design.preview_image = false;
  data_protected_design.preview_src = '';
  data_protected_design.protection_type = '';
  data_protected_design.state_dragdropfile = false;
  data_protected_design.loader = false;
  data_protected_design.html_grouped_file = '';
  data_protected_design.text_grouped_file = '';

  sessionStorage.clear();
}



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
        if(app.location == 'home') {
          data.state_dragdropfile = true;
          app.$refs.app_home.file_input_event();
        }
        if(app.location == 'verify') {
          data_verify.state_dragdropfile = true;
          app.$refs.app_verify.file_input_event();
        }
        if(app.location == 'protected_design') {
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



// Select all text on click
function selectText(containerid) {
  if (document.selection) {
      var range = document.body.createTextRange();
      range.moveToElementText(document.getElementById(containerid));
      range.select();
  } else if (window.getSelection) {
      var range = document.createRange();
      range.selectNode(document.getElementById(containerid));
      window.getSelection().removeAllRanges();
      window.getSelection().addRange(range);
  }
}