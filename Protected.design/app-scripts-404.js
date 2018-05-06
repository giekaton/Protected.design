// If shortlink has the correct length, then try to build protected design page
function loadPD(shortlink) {
    // if (shortlink.length == 12) {
        var href = location.href;
        preview_src_3 = href.substring(href.indexOf("?")+1);

        app.location = 'protected_design';

        Vue.http.get('wp-json/get_protected_design/get', {params: {shortlink: shortlink}}).then(response => {
            // success callback
            if (!response.body){
                app.location = "404";
                return;
            }
            data_protected_design.hash = response.body.hash;
            data_protected_design.message_hex = response.body.message;
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

            // If preview URL provided by the user, generate hash on the backend to compare it with the protected design hash
            var preview_url = response.body.preview_url;

            // Session storage
            if (
                (sessionStorage.getItem('session_preview_src_'+response.body.hash) && response.body.status == 'Payment received') ||
                (sessionStorage.getItem('session_preview_src_'+response.body.hash) && response.body.status == 'Payment declined') ||
                (sessionStorage.getItem('session_preview_src_'+response.body.hash) && response.body.status == 'Payment error')
            ) {
                data_protected_design.preview_text = false;
                data_protected_design.preview_image = true;
                data_protected_design.preview_src = sessionStorage.getItem('session_preview_src_'+response.body.hash);
            }
            else {
                if (preview_url) {
                    if ((preview_url.endsWith('png')) || (preview_url.endsWith('jpg')) || (preview_url.endsWith('jpeg')) || (preview_url.endsWith('gif'))) {
                        data_protected_design.loader = true;

                        Vue.http.get('wp-json/php_hash/get', {params: {preview_src: preview_url}}).then(response => {
                        // success callback
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

                // Generate hash on the backend to compare it with the protected design hash
                else if ((preview_src_3.endsWith('png')) || (preview_src_3.endsWith('jpg')) || (preview_src_3.endsWith('jpeg')) || (preview_src_3.endsWith('gif'))) {
                    data_protected_design.loader = true;

                    Vue.http.get('wp-json/php_hash/get', {params: {preview_src: preview_src_3}}).then(response => {
                    // success callback
                    if (response.body.substring(0,12) == data_protected_design.hash.substring(0,12)) {
                        data_protected_design.preview_text = false;
                        data_protected_design.preview_image = true;
                        data_protected_design.preview_src = preview_src_3;
                    }
                    else {
                        data_protected_design.preview_text = true;
                        data_protected_design.text_file_input = 'The file provided in the URL does not belong to this protected design'
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

            // Submit TX after 3D Secure cc check
            if (data_protected_design.text_status == "Payment received") {
                data_protected_design.text_tx_timestamp = "Please wait...";
                data_protected_design.text_tx_url = "Please wait...";

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
        
                // Get info from db, update front end
                Vue.http.get('wp-json/get_protected_design/get', {params: {shortlink: data_protected_design.hash.substring(0,12)}}).then(response => {                
                    data_protected_design.text_status = response.body.status;
                    data_protected_design.text_tx_hash = response.body.tx_hash;
                    data_protected_design.text_tx_url = etherscan_url + "/tx/" + data_protected_design.text_tx_hash;
                    data_protected_design.text_file_input = 'This design is protected<div style="margin-top:2px;font-size:15px;">Drag and drop or click to select your design file to verify it and generate its preview</div>';
                    
                }, response => {
                    // error callback
                });
            }
        }, response => {
            // error callback
        });

        if ((preview_src_3.endsWith('png')) || (preview_src_3.endsWith('jpg')) || (preview_src_3.endsWith('jpeg')) || (preview_src_3.endsWith('gif'))) { 
            data_protected_design.preview_text = true;
            data_protected_design.text_file_input = 'Comparing IDs and generating preview. Please wait...';
        }
    // }
}



// Navigation
function navigation() {
    var href = location.href;
    shortlink = href.match(new RegExp(".design/" + "(.*)"));
    shortlink = shortlink[1];
    if (
        shortlink == 'faq' || 
        shortlink == 'examples' ||
        shortlink == 'verify' ||
        shortlink == 'terms' ||
        shortlink == 'contacts' ||
        shortlink == 'privacy'
    ) {
        app.location = shortlink;
    }
    else if (shortlink == '') {
        app.location = 'home';
        pd_false();
        window.scrollTo(0, 0);
    }
    else {
        if (shortlink.length == 12) {
            loadPD(shortlink);
            return;
        }
        else if (shortlink.indexOf('?') > -1) {
            // preview_src_3 = shortlink.substring(shortlink.indexOf("?")+1);
            shortlink = shortlink.substr(0, shortlink.indexOf('?'));
            if (shortlink.length == 12) {
                loadPD(shortlink);
                return;
            }
        }
        else {
            app.location = "404";
        }
    }
}  
navigation();

// Navigation listener
window.addEventListener('popstate', function(event) {
    navigation();
}, false);



// // Not found page
// if (shortlink.length != 12 || shortlink != data_protected_design.hash.substring(0, 12)) {
//     app.home = false;
//     app.notfound = true;
// }