<?php /* Template Name: Protected.design - Home */ get_header(); ?>

<div id="pre-load"></div>

<div id="app" >
	<div id="body-overlay"></div>

	<div id="container-outer">
	<app-header></app-header>

	<div id="container">

		<div style="margin: 0 auto;max-width:900px;">

			<app-home ref="app_home" v-if="location == 'home'"></app-home>

			<app-protected-design  ref="app_protected_design" v-if="location == 'protected_design'"></app-protected-design>

			<app-examples v-if="location == 'examples'"></app-examples>

			<app-faq v-if="location == 'faq'"></app-faq>

			<app-terms v-if="location == 'terms'"></app-terms>

			<app-terms-pd v-if="terms_pd"></app-terms-pd>

			<app-privacy v-if="location == 'privacy'"></app-privacy>

			<app-contacts v-if="location == 'contacts'"></app-contacts>

			<app-certificate v-if="certificate"></app-certificate>

			<app-cease v-if="cease"></app-cease>

			<app-404 v-if="location == '404'"></app-404>		

			<app-verify ref="app_verify" v-if="location == 'verify'"></app-verify>

		</div>
	</div>
	</div>
	<app-footer></app-footer>
</div>




<template id="app-header-template">
	<div class="app-header">

		<div style="max-width:900px;margin: 0 auto;">

			<div style="float:left;width:180px;display:inline;">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/logo-white.svg?v=002" id="header-logo" v-on:click="home">
			</div>

			<div id="header-menu">
				<span v-on:click="examples" style="margin-right:40px;" class="white-link">Examples</span>
				<span v-on:click="faq" style="margin-right:40px;" class="white-link">FAQ</span>
				<span v-on:click="verify" class="header-verify">
					<span class="header-verify-span">Verify</span>
				</span>
			</div>

		</div>

	</div>
</template>



<template id="app-home-template">
			<div class="app-home">

				<p style="margin-top:70px;font-size:16px;font-weight:bold;">
				Create a blockchain-based proof of design existence and ownership
				</p>
				<br>
				<br>
				<div class="template-content">
	
					<div class="dragdropcontainer dragdropcontainer-hover" id="dragdropcontainer" v-on:click="file_input_click" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragleave="dragLeaveHandler(event);">
						<div style="font-size: 17px;">{{ text_file_input }}</div>
							<input type="file" id="file_input" style="display:none;" @change="file_input_event">

					</div>

					<div v-if="state_file_selected" transition="expand" class="dragdropcontainer" style="margin-top: 10px;padding-top:40px;padding-bottom:37px;cursor:default;">
						<div class="pd_hash">
							The unique ID that represents your file:
							<br>
							<b style="font-size:16px;">{{ text_hash }}</b>
							<br><br>
							Message (optional):<br>
							<input class="message" id="pd_message" type="text" maxlength="64" @click="restrict_characters">
							<span v-show="text_input_error.length > 0" style="color:red;font-size:14px;">
							<br>
							{{ text_input_error }}
							</span>
							<br>
							<br>
							Design's file URL for visual preview (optional):<br>
							<input class="message" id="pd_preview_url" type="text" maxlength="256">
							<br>
							<br>
							<div style="margin-bottom:0px;">Visit the page of your design to enable its protection:</div>
							<div>
							<b style="font-size:18px;" v-on:click="generate_pd" v-html="html_linkhash"></b>
							</div>
						</div>
					</div>

					<p style="margin-top:60px;">
						<span style="font-size:16px;font-weight:bold;">How it works</span>
						<br><br>
						When you select a file, Protected.design generates a unique ID (SHA-256 hash) that represents that file. The ID is generated 
						on your local device. The file itself is not being uploaded to the internet, and its contents are not seen by anyone. This 
						unique ID represents your file's integrity. There is no other file in existence with the same ID. 
						<br><br>
						If you change your design's file even slightly, the file's ID will also change, and your protection will be lost. 
						That is why you should keep a copy of the design's file that you are going to protect. Using the same file, you can verify the protection later.
						<br><br>
						All file formats and file sizes are accepted, but if you want to have a preview of your design, then simply select .png, 
						.gif or .jpg file formats. Protected.Design does not store your files, but if you want an always-visible preview, then 
						provide a URL link to the design's file. 
						<br><br>
						You can also include a text message (e.g., author's name, max. 64 ASCII characters) to be added to the blockchain 
						next to your design's ID.
						<br><br>
						The app adds the ID and the text message (if provided) to the Ethereum transaction on the 
						blockchain, with the date and time the data was added. This blockchain-based information is immutable and 
						incorruptible, and it never expires. By having this information on the blockchain, you have proof that the 
						design file, represented by the particular ID, existed prior to a specific date and that you are its owner. 
						You can then use this mathematically-based proof to legally defend your original work. 
						<br><br>
						For the service, Protected.design charges a US$5.00 fixed fee. This also covers the fee of the Ethereum 
						transaction, paid by Protected.design directly to the Ethereum network.
						<br><br>
						A free version is also available. Designs submitted every week for free protection are then grouped and protected
						together in a single transaction every Sunday GMT 00:00. In this case, Protected.design covers the transaction fee.
					</p>
					

				</div>

			</div>
</template>



<template id="app-protected-design-template">
			<div class="app-protected-design">

				<div style="text-align: center;margin-top:40px;margin-bottom:10px;">

					<p>
						<a :href="text_design_url" class="pd_url" style="color:white;border-bottom:0px;">{{ text_design_url }}</a>
					</p>

				</div>
					<div v-if="preview_image" class="dragdropcontainer_image">
						<div v-bind:class="[loader ? class_preview_image_loader : '', class_preview_image]">
							<img v-if="preview_src != ''" :src="preview_src" class="preview">
							<div v-else style="padding-top:70px;padding-bottom:70px;font-size:17px;color:#ffffff;">File successfully verified. Preview is not available for this file format</div>
						</div>
					</div>


					<div v-if="preview_text" class="dragdropcontainer dragdropcontainer-hover" id="dragdropcontainer" v-on:click="file_input_click" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragleave="dragLeaveHandler(event);">
						<div class="preview-false">
							<div class="dragdropcontainertext">
								<div v-html=text_file_input></div>
								<input type="file" id="file_input" style="display:none;" @change="file_input_event">
							</div>
						</div>
					</div>


					<div v-if="preview_false" class="dragdropcontainer dragdropcontainer-hover" id="dragdropcontainer" v-on:click="file_input_click" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragleave="dragLeaveHandler(event);">
						<div class="preview-text">
							<div class="dragdropcontainertext">
								{{ text_file_input }}
								<input type="file" id="file_input" style="display:none;" @change="file_input_event">
							</div>
						</div>
					</div>
				

				<p style="text-align:left;">
					The unique ID: <b>{{hash}}</b>
					<br>
					File size: {{text_file_size}}
					<span v-if="text_message_ascii != ''"><br>
					Message: {{text_message_ascii}}</span>
					<br>
					<br>
					Status: <b>{{text_status}}</b>
					<br>
					<div v-if="text_status=='Ready for protection'">
						Choose your protection:<br>
							<label>
								<div class="protection-type">
									<input type="radio" value="1" v-model="protection_type">Individual & fast (US$5.00)
								</div>
							</label>
							<label>
								<div class="protection-type">
									<input type="radio" value="2" v-model="protection_type">Grouped & slow (Free)
								</div>
							</label>
							<div style="clear:both;padding:10px;"></div>
					</div>

					<div v-if="text_status=='Payment declined'">
					Your payment has been declined. You can try again.<br><br>
						Choose your protection:<br>
							<label>
								<div class="protection-type">
									<input type="radio" value="1" v-model="protection_type">Individual & fast (US$5.00)
								</div>
							</label>
							<label>
								<div class="protection-type">
									<input type="radio" value="2" v-model="protection_type">Grouped & slow (Free)
								</div>
							</label>
							<div style="clear:both;padding:10px;"></div>
					</div>


					<div v-if="text_status=='Payment error'">
					<span style="color:red;">Your payment has encountered an error. You can try again.</span><br><br>
						Choose your protection:<br>
							<label>
								<div class="protection-type">
									<input type="radio" value="1" v-model="protection_type">Individual & fast (US$5.00)
								</div>
							</label>
							<label>
								<div class="protection-type">
									<input type="radio" value="2" v-model="protection_type">Grouped & slow (Free)
								</div>
							</label>
							<div style="clear:both;padding:10px;"></div>
					</div>


					<span v-if="protection_type=='1' && text_status=='Protected'">
					Protection date: <span v-if="text_tx_timestamp == ''">Please wait...</span><span v-else>{{ text_tx_timestamp }}</span>
					<br><br>
					Transaction: 
					<span v-if="text_tx_hash == ''">Please wait...</span><span v-else><a :href="text_tx_url" 
					target="_blank">{{ text_tx_hash }}</a></span>
					<br><br>
					This design's file ID has been permanently added to the above transaction's “Input Data” field on the Ethereum blockchain. Click on the transaction link to see the design's data.<span v-if="text_message_ascii != ''"> To be able to see the message, click ”Convert to UTF8” button.</span>
					<span v-if="text_tx_timestamp == 'Please wait...' || text_tx_timestamp == ''">
						<br><br>
						<i style="color:grey;">Please allow up to 10 minutes for the transaction to appear on Etherscan.</i>
					</span>
					<br><br><br>
					<div class="pd-containers">
						Download a PDF certificate for your personal archive.<br>
						<button id="certificate-button" class="pd-buttons" v-on:click="certificate">Generate certificate</button>
					</div>
					
					<div class="pd-containers">
						Someone using your design without your permission?<br>
						<button id="certificate-button" class="pd-buttons" v-on:click="cease">Generate cease and desist letter</button>
					</div>
					<div style="clear:both;"></div>
					<br>
					</span>

					<span v-if="protection_type=='2' && text_status=='Protected'">
					Protection date: <span v-if="text_tx_timestamp == ''">Please wait...</span><span v-else>{{ text_tx_timestamp }}</span>
					<br><br>
					This design's unique ID<span v-if="text_message_ascii != ''"> and the message (in HEX format)</span> is included in the following group of designs: <span v-html="html_grouped_file"></span>
					<br><br>
					This group's unique ID (which is also the file name of the .txt file) is permanently added to the following transaction's “Input Data” field on the Ethereum blockchain: 
					<a :href="text_tx_url" 
					target="_blank">{{ text_tx_hash }}</a>
					<br><br><br>
					<div class="pd-containers">
						Download a PDF certificate for your personal archive.<br>
						<button id="certificate-button" class="pd-buttons" v-on:click="certificate">Generate certificate</button>
					</div>
					
					<div class="pd-containers">
						Someone using your design without your permission?<br>
						<button id="certificate-button" class="pd-buttons" v-on:click="cease">Generate cease and desist letter</button>
					</div>
					<div style="clear:both;"></div>
					<br>
					</span>

				</p>

				<div v-if="text_status=='Pending'" v-bind="check_status()">
				Your transaction is being processed. Please wait...
				</div>

				<div v-if="text_status=='Payment received'" v-bind="check_status()">
				Your transaction is being processed. Please wait...
				</div>

				<div v-if="text_status=='Error'">
				This transaction has encountered an error. Please contact us by email: <a href="&#109;&#x61;i&#x6c;t&#x6f;:&#x69;n&#102;&#x6f;&#64;&#x70;r&#x6f;t&#x65;c&#116;&#x65;&#100;&#x2e;d&#x65;s&#x69;g&#x6e;">info&#64;&#112;&#x72;&#x6f;&#x74;&#x65;&#x63;&#x74;ed.de&#115;&#105;&#x67;&#x6e;</a>
				</div>

				<div v-if="text_status=='Scheduled'">
				This design is scheduled for the next grouped protection which will occur on Sunday GMT 00:00. 
				Please come back later.
				<br><br><br>
				You can also trigger the grouped protection sooner by donating any amount greater than 0.001 ETH 
				to the Ethereum address provided below. After the donation, the protection will be initiated during the next 5 minutes.
				<br><br>
				Donation address: <b>{{ text_donations_address }}</b>
				</div>

				<div v-show="protection_type=='1'">
					<div v-show="text_status=='Ready for protection' || text_status=='Payment declined' || text_status=='Payment error'" >
					<h2 style="color:black;">Individual design protection</h2>
					It costs only US$5.00 to protect the design. This design's file ID (and message, if provided) will be permanently added 
					to the Ethereum transaction's “Input Data” field on the blockchain. The US$5.00 fee also covers the Ethereum transaction 
					cost. The design’s author, or anyone else, can pay this fee. The timestamp for design protection will be available as soon 
					as the payment is received and the Ethereum transaction is broadcast to the network.
					<br>
					<br>
					<i style="color:grey;">Testing mode: Card nr.: 4111111111111111, Exp. date: 12/24, CVV: 123.</i>
					<br><br>
					<section class="creditly-wrapper gray-theme">
					<div style="background:#424242;color:#FFF;font-size:16px;font-weight:bold;padding:14px;margin:0;height:32px;padding-top:4px;">
						<div style="display:inline;float:left;padding-top:2px;">Pay with card</div>
						<div style="display:inline;float:right;">
							<div class="cc-icons">
								<svg class="cc-icon" >
									<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-discover"></use>
								</svg>
							</div>
						
							<div class="cc-icons">
								<svg class="cc-icon">
									<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-american-express"></use>
								</svg>
							</div>

							<div class="cc-icons">
								<svg class="cc-icon">
									<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-maestro"></use>
								</svg>
							</div>

							<div class="cc-icons">
								<svg class="cc-icon">
									<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-master-card"></use>
								</svg>
							</div>

							<div class="cc-icons">
								<svg class="cc-icon">
									<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-visa"></use>
								</svg>
							</div>
						</div>
					</div>
					<i>
						<div class="card-type" style="text-align:right;margin-top:10px;margin-right:10px;min-height:20px;margin-bottom:-15px"></div>
					</i>
					<div class="credit-card-wrapper">
						<div class="first-row form-group">
						<div class="col-sm-8 controls">
							<label class="control-label">Card Number</label>
							<input class="number credit-card-number form-control"
							type="text" name="number"
							pattern="\d*"
							inputmode="numeric" autocomplete="cc-number" autocompletetype="cc-number" x-autocompletetype="cc-number"
							placeholder="&#149;&#149;&#149;&#149; &#149;&#149;&#149;&#149; &#149;&#149;&#149;&#149; &#149;&#149;&#149;&#149;">
							<div id="ccError" class="checkout-errors"></div>
						</div>
						<div class="col-sm-4 controls">
							<label class="control-label">CVV</label>
							<input class="security-code form-control"Â·
							inputmode="numeric"
							pattern="\d*"
							type="text" name="security-code"
							placeholder="&#149;&#149;&#149;">
							<div id="cvvError" class="checkout-errors"></div>
						</div>
						</div>
						<div class="second-row form-group">
						<div class="col-sm-8 controls">
							<label class="control-label">Name on Card</label>
							<input class="billing-address-name form-control" id="cardHolder"
							type="text" name="name"
							placeholder="John Smith"
							maxlength="32">
							<div id="holderError" class="checkout-errors"></div>
						</div>
						<div class="col-sm-4 controls">
							<label class="control-label">Expiration</label>
							<input class="expiration-month-and-year form-control"
							type="text" name="expiration-month-and-year"
							placeholder="MM / YY">
							<div id="expError" class="checkout-errors"></div>
						</div>
						</div>
					</div>
					</section>

					<div style="margin-bottom:10px;">
						<div style="margin-bottom:5px;">
							<input class="pd-checkbox" id="agree" type="checkbox">I have read and agree with the <span class="yellow_link" v-on:click="terms_pd">Terms and Conditions</span>
						</div>
						Amount to be paid: <b>5.00 USD</b>
						<div id="paymentError" style="font-size:14px;margin-top:8px;" class="checkout-errors"></div>
					</div>
					
					<div style="display: inline; float: left;">
						<button id="submit-button" class="protect-button">Protect this design</button>
					</div>
					<div id="cc_credentials">
						Payments are securely processed by <a href="https://cardinity.com/" target="_blank">Cardinity</a><br>
						Your connection is protected with <a href="https://www.ssllabs.com/ssltest/analyze.html?d=protected.design&latest" target="_blank">Let's Encrypt</a>
					</div>
					<div style="clear:both;margin-bottom:50px;">&nbsp;</div>
					</div>
				</div>

				<div v-show="protection_type=='2'">
					<div v-show="text_status=='Ready for protection' || text_status=='Payment declined' || text_status=='Payment error'" >
					<h2 style="color:black;">Grouped design protection</h2>
					Designs that are submitted every week for free protection are then grouped and protected 
					together in a single transaction on every Sunday GMT 00:00. In this case, Protected.design covers the transaction fee.
					<br>
					<br>
					<div style="margin-bottom:5px;">
						<input class="pd-checkbox" id="agree-2" type="checkbox">I have read and agree with the <span class="yellow_link" v-on:click="terms_pd">Terms and Conditions</span>
					</div>
				
					<div style="display: inline; float: left; width: 280px;">
						<button id="submit-button-2" class="protect-button">Protect this design</button>
					</div>
					<div style="clear:both;margin-bottom:50px;">&nbsp;</div>
					</div>
				</div>

			</div>
</template>





<template id="app-footer-template">
	
	<div class="app-footer">

		<hr>

		<div style="margin-bottom:5px;">
			<a class="blue_link" href="https://facebook.com/protected.design" target="_blank">Facebook</a> | 
			<a class="blue_link" href="https://twitter.com/protecteddesign" target="_blank">Twitter</a> | 
			<a class="blue_link" href="https://github.com/dziungles/protected.design" target="_blank">GitHub</a><br>
		</div>

		<div style="margin-bottom:5px;">
			<span class="blue_link" v-on:click="terms">Terms & Conditions</span> | 
			<span class="blue_link" v-on:click="privacy">Privacy Policy</span> | 
			<span class="blue_link" v-on:click="faq">FAQ</span> | 
			<span class="blue_link" v-on:click="contacts">Contacts</span>
		</div>

		<div style="margin-top:3px;">Protected.design V.0.8</div>
		
	</div>
</template>



<template id="app-404-template">
	
	<div class="app-component">

	<p style="font-weight:bold;font-size:17px;text-align:center;padding-top:40px;">404: Page not found</p>
		
	</div>
</template>



<template id="app-verify-template">
	<div class="app-component">

		<div class="verify-container">

			<div style="text-align: center;margin-top:60px;margin-bottom:10px;">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/logo.svg?v=002" width="190" style="margin-top:10px;margin-bottom:0px;">
			</div>

			<div class="dragdropcontainer dragdropcontainer-hover" id="dragdropcontainer" v-on:click="file_input_click" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragleave="dragLeaveHandler(event);">
				<div style="font-size: 17px;">{{ text_file_input }}</div>
					<input type="file" id="file_input" style="display:none;" @change="file_input_event">
				</div>
			<p style="text-align:center;">
				{{ text_verify_result }}<br>
				<span style="font-size:16px;position:relative;top:5px;" class="linkhash" v-if="text_verify_link != ''" v-on:click="generate_pd">{{ text_verify_link }}<span>
			</p>
			<br>
		</div>
		
	</div>
</template>



<template id="app-examples-template">
	<div class="app-component">

		<p><h3>Examples</h3></p>

		<p>
		<b>Indivudual protection</b>
			<br>
			<br>
			<span v-on:click="examples_link('1460832b5ceb')" class="yellow_link">Logo</span>, 
			<span v-on:click="examples_link('5a74b3a81294')" class="yellow_link">Book</span>, 
			<span v-on:click="examples_link('0605bd6d5f4f')" class="yellow_link">Illustration</span>, 
			<span v-on:click="examples_link('0b783ba6f87e')" class="yellow_link">Lettering</span>, 
			<span v-on:click="examples_link('0a4eb89e28f6')" class="yellow_link">Poster</span>, 
			<span v-on:click="examples_link('1c6b3ca954d1')" class="yellow_link">Game</span>, 
			<span v-on:click="examples_link('31eb6f5535d2')" class="yellow_link">Statuette</span>, 
			<span v-on:click="examples_link('2643099368a3')" class="yellow_link">Photo</span>
			<br>
			<br>
			<br>
			<br>
		<b>Grouped protection</b>
			<br>
			<br>
			<span v-on:click="examples_link('2725aeccd88e')" class="yellow_link">Puzzle</span>, 
			<span v-on:click="examples_link('f3d129ee29a8')" class="yellow_link">Sculpture</span>, 
			<span v-on:click="examples_link('12d007f0db13')" class="yellow_link">App icon</span>, 
			<span v-on:click="examples_link('2f9eb15e7875')" class="yellow_link">Fine art</span>
			<br>
			<br>
			<br>
			<br>
		<b>Visual preview examples</b>
			<br>
			<br>
			You can protect any design files. If the protected file is not in .jpg, .png or .gif format, then it will not have a visual preview. 
			You can still protect the file, and you will be able to verify it. In the example below, the protected file is in Adobe Illustrator format. You can 
			download this example file and then verify it on its Protected.design page.
			<br>
			<br>
			<span v-on:click="examples_link('ddea2231af6e')" class="yellow_link">Adobe illustrator example</span> [ <a href="https://protected.design/img/example.ai">example.ai</a> ]
			<br>
			<br>
			<br>
			An image preview is never stored on Protected.Design’s servers, but it can be associated with a design and loaded remotely every time you open the design's 
			page. If the file is in .png, .jpg, or .gif format and is not bigger than 15 MB, it will be automatically loaded on the protected design's page. While the 
			design's status is "Waiting for payment", the file's preview URL can be modified – simply submit the same file again, but with a different preview URL. 
			<br>
			<br>
			<span v-on:click="examples_link('0f11af8e50e1')" class="yellow_link">With associated image preview</span>
			<br>
			<br>
			<br>
			In the example below, the image preview file is not associated with the protected design but is provided as an external resource after the "?" 
			sign in the protected design's URL.
			<br>
			<br>
			<a href="https://protected.design/14ca7d4d6afb?https://i.imgur.com/wOtnqKP.jpg">https://protected.design/14ca7d4d6afb?https://i.imgur.com/wOtnqKP.jpg</a>
			<br>
			<br>
		</p>
		</div>

	</div>
</template>



<template id="app-faq-template">
<div class="app-component">
	<p><h3>Frequently Asked Questions</h3></p>
	<p>
		<h4>Why should I protect my design?</h4>
		
		Your design or other types of original work can be stolen and used without your permission. As an owner of the work, you 
		can forbid this type of illegal use of the intellectual property, but for this, you need to have a proof that you 
		are the author or have the licensing rights to the original work.
		<br><br>
		Protected.design allows you to create such proof on the blockchain in an easy way, without the need for third parties 
		or complicated registration procedures. Blockchain based proofs are immutable and incorruptible, and they never expire.

	</p>
		
	<p>
		<h4>How does Protected.design work?</h4>
		
		When you select a file, Protected.design generates the unique ID (SHA-256 hash) that represents that file. The ID is generated 
		on your local device, the file itself is not being uploaded to the internet, and its contents are not seen by anyone. This unique ID 
		represents your file's integrity. There is no other file in existence that has the same ID.
		<br>
		<br>
		You can also include a text message that will be added to the blockchain next to your design's ID.
		<br>
		<br>
		The app then allows you to add the ID and the text message (if provided) to the Ethereum transaction on the blockchain and it shows the date when this data 
		was added. This blockchain based information is immutable and incorruptible, and it never expires. By having this information on the 
		blockchain, you have a proof that the design file, represented by the particular ID, existed prior to a specific date and that you are 
		the author of the design. You can then use this proof to legally defend your original work.
		<br>
		<br>
		For this service, Protected.design charges a 5.00 USD fixed fee. This also covers the fee of the Ethereum transaction which is
		paid by Protected.design directly to the Ethereum network.
		<br>
		<br>
		A free version is also available. Designs that are submitted every week for free protection are then grouped and protected
		together in a single transaction on every Sunday GMT 00:00. In this case, Protected.design covers the transaction fee.
		<br>
		<br>
		Example: John selects a design file that has the ID 'd4c8a0fe65951c6531017761146d5a716b073e64' and then adds the message '© Jordan Whitfield'. 
		When he adds this design's data to the blockchain, he gets the transaction nr, in this case it is 0x7f78d6890f85639f9d4c66401f6b9e9cb62256266440e146d07a3a8478e8ed90. 
		He can view this transaction on <a href="https://ropsten.etherscan.io/tx/0x7f78d6890f85639f9d4c66401f6b9e9cb62256266440e146d07a3a8478e8ed90" target="_blank">
		Etherscan</a> (or any other Ethereum block explorer). The data of his design was added to the transaction's “Input Data” field, 
		first the desing's file ID and then the message. The message can be viewed when converted to ASCII mode.
		<br>
		<br>
		<div style="max-width:702px;">
		<img src="https://protected.design/img/pd_example.png" width="100%">
		</div>
	</p>

	<p>
		<h4>If Protected.design will shut down or be unreachable, what will happen to my design's protection?</h4>
		
		Even if Protected.design is not reachable, your design's protection is never lost. It can be easily viewed and verified 
		directly on the Ethereum blockchain, bypassing Protected.design entirely.
	</p>	
	<p>
		<h4>Can the design's protection expire?</h4>
		
		Your design's protection has no expiration date, it is permanent. Even if Ethereum blockchain stops adding new data, 
		it will still be archived by people and be accessible for as long as you can imagine.
	</p>	
	<p>
		<h4>Can someone change or corrupt my design's protection or can it be lost in any way?</h4>
		
		The unique quality of the blockchain is that it is immutable and incorruptible, and because of its 
		<a href="https://en.wikipedia.org/wiki/Trusted_timestamping#Decentralized_timestamping_on_the_blockchain" target="_blank">decentralized</a> nature, 
		the data you add to the blockchain can never be lost.
		<br><br>
		Theoretically, there is always a chance that an unknown flaw of blockchain will be discovered or that some new 
		technology will make it obsolete, but from the beginning of the blockchain technology (2008), this has never happened,
		even though there were <a href="https://www.youtube.com/watch?v=Rw8W92iIHZ8" target="_blank">countless attempts</a> 
		to break it one way or another.
	</p>	
	<p>
		<h4>Is a blockchain based design protection accepted as legally valid evidence in courts around the globe?</h4>
		
		Yes, it is. Below is the excerpt from a 2017, Oct 13 
		<a href="http://www.grantthornton.com.mt/insights/blockchain-technology-and-intellectual-property-ownership/" target="_blank">article</a>, 
		published on the Grant Thornton website.
		<br><br>
		"With expert testimony, the purely mathematical strength of a blockchain certificate can be presented in court everywhere. 
		The main concern, however, is that the application is also not yet widespread enough to prescribe legal validation. 
		<br><br>
		Thankfully many countries are already taking the necessary steps, having recognised the convenience of the blockchain as a public registry. 
		Legislators in many countries such as the US, Sweden, Japan, Brazil, UK and Dubai are pushing forward several initiatives to 
		acknowledge evidence and records based on blockchain and distributed ledger technologies. The EU is heading in the same direction, and 
		the current eIDAS regulation (an EU regulation on electronic identification and trust services for electronic transactions in the 
		internal market) already prohibits courts from denying the legal admissibility of timestamps as evidence on sole grounds that the 
		timestamp does not meet the more stringent requirements of an EU-qualified timestamp."
		<br><br>
		Another promising recent <a href="http://www.wipo.int/wipo_magazine/en/2018/01/article_0005.html" target="_blank">article</a> 
		from World Intellectual Property Organization. 
	</p>	
	<p>
		<h4>How can I prove that I am the author of a particular protected design?</h4>
		
		There are three ways how you can do this:
		<br><br>
		1. You can include author's name in the message field. The message will be added to the blockchain next to the design's ID. 
		The message can only be in <a href="https://www.w3schools.com/charsets/ref_html_ascii.asp" target="_blank">ASCII</a> characters 
		and maximum 64 characters long. On the blockchain, it is presented in a Hexidecimal format and can be viewed in plain text 
		when converted back to the ASCII.
		<br><br>
		2. You can add the author's name on the design's image itself.
		<br><br>
		3. You can keep the original file, never share it, and when you need to prove the fact that you are the owner, you can verify it on Protected.design 
		or directly on the blockchain.
	</p>	
	<p>
		<h4>How can I defend my design if I see the infringement?</h4>
		
		Open your protected design's page and generate cease and desist letter. Then email this letter to the person or company which is infringing your
		intellectual property rights. Attach your original design and also screeshots where the infringer is using it without your permission.
		<br><br>
		If the person or company does not collaborate, then send this letter to the authorities, e.g., the hosting provider of the website
		or use the copyright infringement form to contact Facebook, YouTube or any other web platform if the intellectual property infringement is on any of these sites.
		The illegal use of your intellectual property will then be taken down by the authorities.
	</p>	
	<p>
		<h4>What kind of designs can be protected?</h4>
		
		Any kind of original works that fall under copyrights law can be protected.
		<br><br>
		Apart from copyright protection, you can also protect ideas, sketches, prototypes... Even though ideas, in general, 
		are not eligible for the copyright protection, you can still use a blockchain proof to show that you are the author of the idea. 
		This can be a significant proof, if not on legal, then at least on moral grounds.
	</p>	
	<p>
		<h4>What's the big deal about not disclosing design file's contents?</h4>
		
		As mentioned earlier, when you select your design file, it is not being uploaded to Protected.design servers or disclosed to anyone 
		in any way. For the visual preview, the design is loaded from your local disk or the external url that you provide. Protected.design
		never stores your data or your files. The only data we store, is your design's ID, your file's size, the message (if provided) and
		Ethereum transaction hash. This data does not reveal your identity in any way (if you do not reveal it in the message) and it
		does not reveal your file's contents.
		<br><br>
		This is important firstly because it allows your design's protection function independently from Protected.design.
		<br><br>
		Secondly, there can be many cases when the owner does not want to disclose the contents of a new and unique design or idea. The 
		non-disclosure is guaranteed by the technology of Protected.design. You can check our source code on <a href="https://github.com/dziungles/protected.design" target="_blank">
		GitHub</a> and see that this is true.
	</p>	
	<p>
		<h4>How is design's ID generated? Is it enough to add to the blockchain only the ID and not the file itself?</h4>
		
		The ID is a <a href="https://en.wikipedia.org/wiki/SHA-2" target="_blank">SHA-256</a> hash. 
		You can generate this hash independently as SHA-256 is a well known and standardized hashing algorithm.
		<br><br>
		When you hash a file, you get a unique string that represents the integrity of your file. If you change your file even slightly, the 
		hash that represents that file will be completely different. If you can prove that a particular hash existed on a specific date, 
		it means that the design file, represented by that hash, also existed.
		<br><br>
		You can create a similar SHA-256 hash from your file without the need of using Protected.design, e.g., by using this online 
		<a href="https://emn178.github.io/online-tools/sha256_checksum.html" target="_blank">SHA-256 hash generator</a>.
		<br><br>
		To add only the file's hash (ID) is entirely sufficient proof for the design's protection because there is no other file in existence with the same ID. 
		To add the whole file to the blockchain is possible, but that would be highly unpractical. Firstly, because of the privacy
		reasons - the file's contents would be revealed to everyone. Secondly, adding data to the blockchain is highly expensive because
		of the distributed nature of the technology. To add the whole image file to the blockchain would cost thousands of dollars.
	</p>	
	<p>
		<h4>How is the design's visual preview generated?</h4>
		
		When the user selects a design file from a local device, the file is not uploaded to Protected.design but stored locally in browser's cache 
		and the preview is loaded from this stored file. If the user refreshes the page, the local cache clears, and the preview disappears.
		<br><br>
		When submiting new design for protection, the user can also provide a link to the same file. If the file is in png, jpg, or gif format and is not 
		bigger than 15 MB, it will be automatically loaded in protected design's page. While design's status is 'Waiting for payment', the link can be modified similarly as 
		the message - just submit the same file again, but with a different preview url.
		<br><br>
		If the user removes the file from the url that is provided in Protected.design, then the design's page will load similarly as if there is no preview 
		file url provided.
		<br><br>
		Alternatively, the preview file can be added to the link after the "?" sign. Our back-end PHP script will fetch the file and generate its hash (ID). It then checks it
		against the particular design's ID, and if they match it then sends image file data back to the front-end to render the preview. Even though the file is 
		fetched by our back-end script, it is never saved on the server. All traces of the file on our server disappear as soon as the session ends.
	</p>	
	<p>
		<h4>My question was not answered here. How can I contact Protected.design?</h4>
		
		You can contact us by email: <a href="&#109;&#x61;i&#x6c;t&#x6f;:&#x69;n&#102;&#x6f;&#64;&#x70;r&#x6f;t&#x65;c&#116;&#x65;&#100;&#x2e;d&#x65;s&#x69;g&#x6e;">info&#64;&#112;&#x72;&#x6f;&#x74;&#x65;&#x63;&#x74;ed.de&#115;&#105;&#x67;&#x6e;</a>
	</p>	
</div>
</template>



<template id="app-terms-template">
<div class="app-component">
	<p><h3>Terms and Conditions</h3></p>
	<p>
	Last updated: 18, Apr 2018
	<h4>1. Terms</h4>
	These Terms and Conditions constitute a legally binding agreement made between you, whether personally or on behalf of an entity (“you”) and Protected.design (“we,” “us” 
	or “our”), concerning your access to and use of the https://protected.design website as well as any other media form, media channel, mobile website or mobile application 
	related, linked, or otherwise connected thereto (collectively, the “Site”). You agree that by accessing the Site, you have read, understood, and agreed to be bound by 
	all of these Terms and Conditions. IF YOU DO NOT AGREE WITH ALL OF THESE Terms and Conditions, THEN YOU ARE EXPRESSLY PROHIBITED FROM USING THE SITE AND YOU MUST DISCONTINUE 
	USE IMMEDIATELY.
	<br><br>
	Supplemental Terms and Conditions or documents that may be posted on the Site from time to time are hereby expressly incorporated herein by reference. We reserve the right, 
	in our sole discretion, to make changes or modifications to these Terms and Conditions at any time and for any reason. We will alert you about any changes by updating 
	the “Last updated” date of these Terms and Conditions, and you waive any right to receive specific notice of each such change. It is your responsibility to periodically 
	review these Terms and Conditions to stay informed of updates. You will be subject to, and will be deemed to have been made aware of and to have accepted, the changes in 
	any revised Terms and Conditions by your continued use of the Site after the date such revised Terms and Conditions are posted.
	<br><br>
	The information provided on the Site is not intended for distribution to or use by any person or entity in any jurisdiction or country where such distribution or use would 
	be contrary to law or regulation or which would subject us to any registration requirement within such jurisdiction or country. Accordingly, those persons who choose to 
	access the Site from other locations do so on their own initiative and are solely responsible for compliance with local laws, if and to the extent local laws are applicable.
	<h4>2. Use License</h4>
	Unless otherwise indicated, the Site is our proprietary property and all source code, databases, functionality, software, website designs, text, 
	and graphics on the Site (collectively, the “Content”) and the trademarks, service marks, and logos contained therein (the “Marks”) are owned or controlled by us or 
	licensed to us, and are protected by copyright and trademark laws and various other intellectual property rights and unfair competition laws of the United States, foreign 
	jurisdictions, and international conventions. The Content and the Marks are provided on the Site without warranty on an "as is" and "as available" basis. Any part 
	of the Site may be changed or removed at any time without warning. Except as expressly provided in these Terms and Conditions, no part of the Site and no Content or Marks may 
	be copied, reproduced, aggregated, republished, uploaded, posted, publicly displayed, encoded, translated, transmitted, distributed, sold, licensed, or otherwise exploited 
	for any commercial purpose whatsoever, without our express prior written permission.
	<br><br>
	Provided that you are eligible to use the Site, you are granted a limited license to access and use the Site and to download or print a copy of any portion of the Content 
	to which you have properly gained access for your personal or commercial use. We reserve all rights not expressly granted to you in and to the Site, the Content and the Marks.
	<br><br>
	Any content provided by our users (User Content), such as file hash, message or file size is presented on the Site on behalf of our users. If the user provides a file's url 
	link for the visual preview of that file, then the file itself is not being stored on Protected.design but only loaded remotely for representational purposes. Protected.design 
	does not claim any ownership rights in the User Content but is granted by the user a non-exclusive, royalty-free, worldwide, limited license to use and display the User 
	Content in order for the Protected.design services to function.
	<h4>3. Privacy</h4>
	Please review our Privacy policy, which also governs your visit to our Site, to understand our practices.
	<h4>4. Service Level Agreement</h4>
	Protected.design provides the interface for the users to store the unique identifiers (SHA-256 hashes) of their files on the Ethereum blockchain. 
	The file's hash stored in a transaction on the blockchain is a mathematically based proof that the file existed prior to the date timestamped 
	in that transaction. If the user includes the message with the owner's name visually on the file or in the message field when submitting the file, 
	this is then considered to be a proof of design ownership and can be presented in courts around the globe as a legal evidence.
	<br><br>
	Nevertheless, Protected.design does not take any responsibility for the outcome of the intellectual property dispute regarding any files protected using 
	the Protected.design service. Protected.design allows to create a proof of design existence and its ownership, and generate cease and desist letter, 
	but does not guarantee and makes no claims that the intellectual property dispute can be won on this proof only, as there are many unknown factors that can 
	impact the outcome of the dispute one way or another.
	<br><br>
	You must be at least 13 years old to use Protected.design services.
	<h4>5. Limitations of Liability</h4>
	In no event shall Protected.design or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or 
	due to business interruption) arising out of the use or inability to use the materials on Protected.design's website, even if Protected.design or a 
	Protected.design authorized representative has been notified orally or in writing of the possibility of such damage.
	<br><br>
	You understand and agree that we will not be liable to you or to any third party for any indirect, incidental, special, consequential, or exemplary 
	damages which you may incur, howsoever caused and under any theory of liability, including, without limitation, any loss of profits (whether incurred 
	directly or indirectly), loss of goodwill or business reputation, loss of data, cost of procurement of substitute goods or services, or any other intangible 
	loss, even if we have been advised of the possibility of such damages.
	<br><br>
	You agree that our total, aggregate liability to you for any and all claims arising out of or relating to these terms or your access to or use of 
	(or your inability to access or use) any portion of the Site, whether in contract, tort, strict liability, or any other legal theory, is limited to the 
	greater of (a) the amounts you actually paid us under these terms in the 12 month period preceding the date the claim arose, or (b) $100.
	<br><br>
	You acknowledge and agree that we have made the Site available to you and entered into these terms in reliance upon the warranty disclaimers and limitations 
	of liability set forth herein, which reflect a reasonable and fair allocation of risk between the parties and form an essential basis of the bargain between us. 
	We would not be able to provide the Site to you without these limitations.
	<h4>6. Disclaimer Regarding the Ethereum Blockchain</h4>
	Protected.design is not responsible for losses due to blockchains or any other features of the Ethereum network, including but not limited to late report 
	by developers or representatives (or no report at all) of any issues with the blockchain supporting the Ethereum network, including forks, technical 
	node issues, or any other issues that can negatively impact Protected.design services.
	<br><br>
	Protected.design does not guarantee that the Ethereum blockchain will work indefinitely or that some newly discovered bug or new technology will 
	not make the Ethereum blockchain obsolete. This is not something that can be known in advance or controlled by Protected.design, therefore Protected.design takes no
	responsibility for it.
	<h4>7. Disclaimer Regarding Legal Advice</h4>
	Any advice and/or guidance of a legal nature contained on the Site is purely for information purposes only to assist users in acquainting themselves with 
	the law relating to intellectual property rights and should not be construed as legal advice. Users are obliged to seek proper legal advice before relying 
	on any information contained on the Site.
	<br><br>
	Protected.design will not be responsible for and makes no warranties, express or implied, as to the accuracy and reliability of any advice or other content 
	posted on the Site or otherwise displayed or published in relation to the services.	
	<h4>8. Refunds</h4>
	Given the nature of the digital services provided by Protected.design and considering the several costs involved in creating the Ethereum transaction, 
	we do not offer refunds.
	<br><br>
	In case our main service - adding data to the blockchain, as described in Service level agreement - was not fulfilled and it was a direct cause of Protected.design's 
	application malfunctioning, we guarantee that all steps will be taken to ensure that the service will be fulfilled as soon as possible without any additional 
	costs on the user side. 
	<br><br>
	In any case where your design's data was not successfully added to the blockchain, but the fee was charged, please contact us by email and we will:<br>
	1. Investigate and find a problem why this issue appeared.<br>
	2. Manually broadcast the transaction to the Ethereum network.
	<h4>9. Accuracy of materials</h4>
	The materials appearing on Protected.design website could include technical, typographical, or photographic errors. Protected.design does not warrant 
	that any of the materials on its website are accurate, complete or current. Protected.design may make changes to the materials contained on its website 
	at any time without notice. However Protected.design does not make any commitment to update the materials.
	<h4>10. Links</h4>
	Protected.design has not reviewed all of the sites linked to its website and is not responsible for the contents of any such linked site. The inclusion 
	of any link does not imply endorsement by Protected.design of the site. Use of any such linked website is at the user's own risk.
	<h4>11. Modifications</h4>
	Protected.design may revise these terms of service for its website at any time without notice. By using this website you are agreeing to be bound by 
	the then current version of these terms of service.
	<h4>12. Governing Law</h4>
	These terms and conditions are governed by and construed in accordance with the laws of Lithuania and you irrevocably submit to the exclusive jurisdiction 
	of the courts in that location.
	</p>
</div>
</template>


<template id="app-terms-pd-template">
<div class="app-component-modal">
	<div style="position:fixed;top:12px;right:20px;cursor:pointer;font-size:30px;" v-on:click="terms_pd">&#10005;</div>
	<p><h3>Terms and Conditions</h3>
	Last updated: 18, Apr 2018
	<h4>1. Terms</h4>
	These Terms and Conditions constitute a legally binding agreement made between you, whether personally or on behalf of an entity (“you”) and Protected.design (“we,” “us” 
	or “our”), concerning your access to and use of the https://protected.design website as well as any other media form, media channel, mobile website or mobile application 
	related, linked, or otherwise connected thereto (collectively, the “Site”). You agree that by accessing the Site, you have read, understood, and agreed to be bound by 
	all of these Terms and Conditions. IF YOU DO NOT AGREE WITH ALL OF THESE Terms and Conditions, THEN YOU ARE EXPRESSLY PROHIBITED FROM USING THE SITE AND YOU MUST DISCONTINUE 
	USE IMMEDIATELY.
	<br><br>
	Supplemental Terms and Conditions or documents that may be posted on the Site from time to time are hereby expressly incorporated herein by reference. We reserve the right, 
	in our sole discretion, to make changes or modifications to these Terms and Conditions at any time and for any reason. We will alert you about any changes by updating 
	the “Last updated” date of these Terms and Conditions, and you waive any right to receive specific notice of each such change. It is your responsibility to periodically 
	review these Terms and Conditions to stay informed of updates. You will be subject to, and will be deemed to have been made aware of and to have accepted, the changes in 
	any revised Terms and Conditions by your continued use of the Site after the date such revised Terms and Conditions are posted.
	<br><br>
	The information provided on the Site is not intended for distribution to or use by any person or entity in any jurisdiction or country where such distribution or use would 
	be contrary to law or regulation or which would subject us to any registration requirement within such jurisdiction or country. Accordingly, those persons who choose to 
	access the Site from other locations do so on their own initiative and are solely responsible for compliance with local laws, if and to the extent local laws are applicable.
	<h4>2. Use License</h4>
	Unless otherwise indicated, the Site is our proprietary property and all source code, databases, functionality, software, website designs, text, 
	and graphics on the Site (collectively, the “Content”) and the trademarks, service marks, and logos contained therein (the “Marks”) are owned or controlled by us or 
	licensed to us, and are protected by copyright and trademark laws and various other intellectual property rights and unfair competition laws of the United States, foreign 
	jurisdictions, and international conventions. The Content and the Marks are provided on the Site without warranty on an "as is" and "as available" basis. Any part 
	of the Site may be changed or removed at any time without warning. Except as expressly provided in these Terms and Conditions, no part of the Site and no Content or Marks may 
	be copied, reproduced, aggregated, republished, uploaded, posted, publicly displayed, encoded, translated, transmitted, distributed, sold, licensed, or otherwise exploited 
	for any commercial purpose whatsoever, without our express prior written permission.
	<br><br>
	Provided that you are eligible to use the Site, you are granted a limited license to access and use the Site and to download or print a copy of any portion of the Content 
	to which you have properly gained access for your personal or commercial use. We reserve all rights not expressly granted to you in and to the Site, the Content and the Marks.
	<br><br>
	Any content provided by our users (User Content), such as file hash, message or file size is presented on the Site on behalf of our users. If the user provides a file's url 
	link for the visual preview of that file, then the file itself is not being stored on Protected.design but only loaded remotely for representational purposes. Protected.design 
	does not claim any ownership rights in the User Content but is granted by the user a non-exclusive, royalty-free, worldwide, limited license to use and display the User 
	Content in order for the Protected.design services to function.
	<h4>3. Privacy</h4>
	Please review our Privacy policy, which also governs your visit to our Site, to understand our practices.
	<h4>4. Service Level Agreement</h4>
	Protected.design provides the interface for the users to store the unique identifiers (SHA-256 hashes) of their files on the Ethereum blockchain. 
	The file's hash stored in a transaction on the blockchain is a mathematically based proof that the file existed prior to the date timestamped 
	in that transaction. If the user includes the message with the owner's name visually on the file or in the message field when submitting the file, 
	this is then considered to be a proof of design ownership and can be presented in courts around the globe as a legal evidence.
	<br><br>
	Nevertheless, Protected.design does not take any responsibility for the outcome of the intellectual property dispute regarding any files protected using 
	the Protected.design service. Protected.design allows to create a proof of design existence and its ownership, and generate cease and desist letter, 
	but does not guarantee and makes no claims that the intellectual property dispute can be won on this proof only, as there are many unknown factors that can 
	impact the outcome of the dispute one way or another.
	<br><br>
	You must be at least 13 years old to use Protected.design services.
	<h4>5. Limitations of Liability</h4>
	In no event shall Protected.design or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or 
	due to business interruption) arising out of the use or inability to use the materials on Protected.design's website, even if Protected.design or a 
	Protected.design authorized representative has been notified orally or in writing of the possibility of such damage.
	<br><br>
	You understand and agree that we will not be liable to you or to any third party for any indirect, incidental, special, consequential, or exemplary 
	damages which you may incur, howsoever caused and under any theory of liability, including, without limitation, any loss of profits (whether incurred 
	directly or indirectly), loss of goodwill or business reputation, loss of data, cost of procurement of substitute goods or services, or any other intangible 
	loss, even if we have been advised of the possibility of such damages.
	<br><br>
	You agree that our total, aggregate liability to you for any and all claims arising out of or relating to these terms or your access to or use of 
	(or your inability to access or use) any portion of the Site, whether in contract, tort, strict liability, or any other legal theory, is limited to the 
	greater of (a) the amounts you actually paid us under these terms in the 12 month period preceding the date the claim arose, or (b) $100.
	<br><br>
	You acknowledge and agree that we have made the Site available to you and entered into these terms in reliance upon the warranty disclaimers and limitations 
	of liability set forth herein, which reflect a reasonable and fair allocation of risk between the parties and form an essential basis of the bargain between us. 
	We would not be able to provide the Site to you without these limitations.
	<h4>6. Disclaimer Regarding the Ethereum Blockchain</h4>
	Protected.design is not responsible for losses due to blockchains or any other features of the Ethereum network, including but not limited to late report 
	by developers or representatives (or no report at all) of any issues with the blockchain supporting the Ethereum network, including forks, technical 
	node issues, or any other issues that can negatively impact Protected.design services.
	<br><br>
	Protected.design does not guarantee that the Ethereum blockchain will work indefinitely or that some newly discovered bug or new technology will 
	not make the Ethereum blockchain obsolete. This is not something that can be known in advance or controlled by Protected.design, therefore Protected.design takes no
	responsibility for it.
	<h4>7. Disclaimer Regarding Legal Advice</h4>
	Any advice and/or guidance of a legal nature contained on the Site is purely for information purposes only to assist users in acquainting themselves with 
	the law relating to intellectual property rights and should not be construed as legal advice. Users are obliged to seek proper legal advice before relying 
	on any information contained on the Site.
	<br><br>
	Protected.design will not be responsible for and makes no warranties, express or implied, as to the accuracy and reliability of any advice or other content 
	posted on the Site or otherwise displayed or published in relation to the services.	
	<h4>8. Refunds</h4>
	Given the nature of the digital services provided by Protected.design and considering the several costs involved in creating the Ethereum transaction, 
	we do not offer refunds.
	<br><br>
	In case our main service - adding data to the blockchain, as described in Service level agreement - was not fulfilled and it was a direct cause of Protected.design's 
	application malfunctioning, we guarantee that all steps will be taken to ensure that the service will be fulfilled as soon as possible without any additional 
	costs on the user side. 
	<br><br>
	In any case where your design's data was not successfully added to the blockchain, but the fee was charged, please contact us by email and we will:<br>
	1. Investigate and find a problem why this issue appeared.<br>
	2. Manually broadcast the transaction to the Ethereum network.
	<h4>9. Accuracy of materials</h4>
	The materials appearing on Protected.design website could include technical, typographical, or photographic errors. Protected.design does not warrant 
	that any of the materials on its website are accurate, complete or current. Protected.design may make changes to the materials contained on its website 
	at any time without notice. However Protected.design does not make any commitment to update the materials.
	<h4>10. Links</h4>
	Protected.design has not reviewed all of the sites linked to its website and is not responsible for the contents of any such linked site. The inclusion 
	of any link does not imply endorsement by Protected.design of the site. Use of any such linked website is at the user's own risk.
	<h4>11. Modifications</h4>
	Protected.design may revise these terms of service for its website at any time without notice. By using this website you are agreeing to be bound by 
	the then current version of these terms of service.
	<h4>12. Governing Law</h4>
	These terms and conditions are governed by and construed in accordance with the laws of Lithuania and you irrevocably submit to the exclusive jurisdiction 
	of the courts in that location.
	<br>
	<br>
	<br>
	<span v-on:click="terms_pd" class="yellow_link">Close</span></p>
</div>
</template>



<template id="app-privacy-template">
<div class="app-component">
	<p><h3>Privacy Policy</h3></p>
	<p>
	Last updated: 18, Apr 2018
	<br><br>
	Protected.design operates https://protected.design (the "Site"). This page informs you of our policies regarding the collection, use and disclosure of Personal 
	Information we receive from users of the Site.
	<br><br>
	We use your Personal Information only for providing and improving the Site. By using the Site, you agree to the collection and use of information in accordance 
	with this policy.
	<h4>Information Collection and Use</h4>
	We do not collect any additional personal information, except the standard Cookies and Log Data listed below.
	<br><br>
	When the user uses the Site, we only collect the file hash and its size in bytes (metadata). This information is publicly available on protected design's page.
	It helps to identify the file, but does not include any personal information or disclose any private contents. 
	<br><br>
	Optionally, the user can provide the message that will be saved in our database, shown on the design's page and included next to the design's hash on the blockchain. 
	We do not control this and take no responsibility for the message's content provided by the user in the design's 'message' field. The user can change or remove the message, 
	while the design's status is 'Waiting for payment'. The update of the message can be done by repeatedly submitting the same design's file.
	<br><br>
	If the user provides a file's url link for the visual preview of that file, then the file itself is not being stored on Protected.design but only loaded remotely 
	for representational purposes. File's url link can be changed similarly as the message while design's status is "Waiting for payment". If the user removes the file from the 
	url that is provided in Protected.design, then the design's page will load similarly as if there is no preview file url provided. 
	<h4>Payment Processing</h4>
	Protected.design uses Cardinity to process payments made for services via the Site. Cardinity is a third party processor that complies with Payment Card Industry Data Security 
	Standards (PCI DSS). Your credit or payment card details are communicated directly from your browser to Cardinity and may be stored by Cardinity. Protected.design does 
	not see or collect this personal information at any time. For more details, please refer to 
	<a href="https://cardinity.com/privacy-policy" target="_blank">Cardinity privacy policy</a>.
	<h4>Log Data</h4>
	Like many site operators, we collect information that your browser sends whenever you visit our Site ("Log Data").
	<br><br>
	This Log Data may include information such as your computer's Internet Protocol ("IP") address, browser type, browser version, the pages of our Site that you visit, the 
	time and date of your visit, the time spent on those pages and other statistics.
	<br><br>
	In addition, we may use third party services such as Google Analytics that collect, monitor and analyze this in order to provide user behavior insights and statistics 
	for our website.
	<h4>Cookies</h4>
	Cookies are files with small amount of data, which may include an anonymous unique identifier. Cookies are sent to your browser from a web site and stored on your 
	computer's hard drive.
	<br><br>
	Like many sites, we use "cookies" to collect information. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent.
	<h4>Security</h4>
	The security of your Personal Information is important to us, but remember that no method of transmission over the Internet, or method of electronic storage, 
	is 100% secure. While we strive to use commercially acceptable means to protect your Personal Information, we cannot guarantee its absolute security.
	<h4>Changes to This Privacy Policy</h4>
	This Privacy Policy is effective as of 18, Apr 2018 and will remain in effect except with respect to any changes in its provisions in the future, which will 
	be in effect immediately after being posted on this page.
	<br><br>
	We reserve the right to update or change our Privacy Policy at any time and you should check this Privacy Policy periodically. Your continued use of the Service 
	after we post any modifications to the Privacy Policy on this page will constitute your acknowledgment of the modifications and your consent to abide and be bound 
	by the modified Privacy Policy.
	<h4>Contact Us</h4>
	If you have any questions about this Privacy Policy, please contact us.
	</p>
</div>
</template>



<template id="app-contacts-template">
<div class="app-component">
	<p><h3>Contacts</h3></p>

<p>
	If you have any questions, please contact us by email: <b><a href="&#109;&#x61;i&#x6c;t&#x6f;:&#x69;n&#102;&#x6f;&#64;&#x70;r&#x6f;t&#x65;c&#116;&#x65;&#100;&#x2e;d&#x65;s&#x69;g&#x6e;">info&#64;&#112;&#x72;&#x6f;&#x74;&#x65;&#x63;&#x74;ed.de&#115;&#105;&#x67;&#x6e;</a></b>
	<br>
	<br>
	Social: 
	<a href="https://facebook.com/protected.design" target="_blank">Facebook</a> | 
	<a href="https://twitter.com/protecteddesign" target="_blank">Twitter</a> | 
	<a href="https://github.com/dziungles/protected.design" target="_blank">GitHub</a><br>
	<br>
	<br>
	<br>
</p>
<br>
</div>
</template>



<template id="app-certificate-template">
<div class="app-certificate">

	<div style="position:fixed;top:7px;right:13px;cursor:pointer;font-size:22px;font-weight:bold;" v-on:click="certificate">&#10005;</div>
	
	<div id="cert-to-pdf">
	
		<p><div style="font-size:20px;font-weight:bold;margin-bottom:10px;">Protected.design certificate</div></p>
		<div v-show="(data_protected_design.preview_image || data_protected_design.preview_src != '') && !data_certificate.previewCors">
			<img :src="data_certificate.preview_src" class="preview-cert" id="preview-cert">
		</div>
		<br>
		Status: <b>{{data_protected_design.text_status}}</b>
		<br>
		Protection date: <b><span v-if="data_protected_design.text_tx_timestamp == ''">Please wait...</span><span v-else>{{ data_protected_design.text_tx_timestamp }}</span></b>
		<br><br>
		<span v-if="data_protected_design.protection_type=='2'">
			File size: <b>{{data_protected_design.text_file_size}}</b>
			<span v-if="data_protected_design.text_message_ascii != ''"><br>
			Message: <b>{{data_protected_design.text_message_ascii}}</b></span>
			<br><br>
		</span>
		Design file's unique ID (hash):<br><b>{{data_protected_design.hash}}</b>
		<br>
		<span v-if="data_protected_design.protection_type=='2'">Design's hash is added to the following text file:<br><b>{{ data_protected_design.text_grouped_file }}</b><br></span>
		Text file's hash is added to the following transaction:<br>
		<b><span v-if="data_protected_design.text_tx_hash == ''">Please wait...</span><span v-else>{{ data_protected_design.text_tx_hash }}</span></b>
		<span v-if="data_protected_design.protection_type=='1'">
			<br><br>
			File size: <b>{{data_protected_design.text_file_size}}</b>
			<span v-if="data_protected_design.text_message_ascii != ''"><br>
			Message: <b>{{data_protected_design.text_message_ascii}}</b></span>
			<br><br>
			This design's file ID has been permanently added to the transaction's “Input Data” field on the Ethereum blockchain. 
			<span v-if="data_protected_design.text_message_ascii != ''"> To be able to see the message, click 'Convert to UTF8' button.</span>
		</span>
		<br>
		<br>
		<span class="pd_url" style="color:white;font-size:14;">{{ data_protected_design.text_design_url }}</span>
		
	</div>
	
	<p style="margin-top: 20px;">
		<span v-html="preview_cors_message"></span>
		<span v-show="savePdfButton == false">Please wait...</span>
		<span v-on:click="savePdf" class="yellow_link" v-show="savePdfButton == true">Save as PDF</span>
		<span v-show="previewCors == true || previewCorsPassed == true"><input type="file" id="file_input_cert" style="display:none;" @change="file_input_event_cert"><span v-on:click="calculateHashCert" class="yellow_link">Select file</span> | <span v-on:click="savePdf(true)" class="yellow_link">Continue</span></span>
	</p>

	<p v-if="data_protected_design.text_grouped_file.length > 5">
		To complete your archive, also download the text file in which your your design's ID is included:<br><span v-html="data_protected_design.html_grouped_file">
	</p>

	<p><span v-on:click="certificate" class="yellow_link">Close</span></p>

</div>
</template>



<template id="app-cease-template">

	<div class="app-component-modal">
		
		<div style="position:fixed;top:12px;right:20px;cursor:pointer;font-size:30px;" v-on:click="cease">&#10005;</div>
		<p><h3>Cease and desist letter generator</h3></p>
		
		If someone is using your design without your permission, use a cease and desist letter to make a formal request for them to stop ("cease") and not continue ("desist") 
		this behavior. Generate a cease and desist letter and send it to the person or company infringing your intellectual property rights. Attach your original design 
		and screenshots showing where/how the infringer is using it without your permission.
		<br><br>
		After receiving this letter, if the infringing person or company does not cease and desist, then you should contact the authorities who can take action  
		against the infringer. For example, if your design is being used on a website, then send the letter to the hosting provider of that website. If your design is being 
		used on YouTube, Facebook or any other social media site, then contact the support for that site, or fill out the copyright infringement form.
		<br><br>
		The same cease and desist letter can be printed and sent to a physical address, preferably as a certified letter.
		<br><br>
		<span style="font-size:12px;">
		DISCLAIMER: THIS CEASE AND DESIST LETTER IS A GENERAL TEMPLATE THAT MIGHT NOT FIT YOUR INDIVIDUAL NEEDS. THE PROVIDED INFORMATION HERE AND THE CEASE AND DESIST LETTER 
		TEMPLATE DOES NOT CONSTITUTE LEGAL ADVICE. USING THIS INFORMATION DOES NOT CREATE AN ATTORNEY-CLIENT RELATIONSHIP BETWEEN YOU AND PROTECTED.DESIGN. IN THE FUTURE, 
		PROTECTED.DESIGN WILL WORK WITH AN IP LAW FIRM AND EXPAND THIS SECTION, BUT FOR NOW, IF YOU WISH TO RECEIVE LEGAL ADVICE, YOU SHOULD HIRE AN ATTORNEY WHO IS 
		ABLE TO ASSISIST WITH YOUR SPECIFIC SITUATION. USING THE PROOF OF DESIGN OWNERSHIP CREATED WITH PROTECTED.DESIGN, THE ATTORNEY WILL TAILOR HIS/HER ADVICE TO YOUR SPECIFIC NEEDS.
		</span>
		<br><br><br>
		<b>Provide details for your cease and desist letter:</b><br><br>
		<div style="line-height:30px;margin-bottom:5px;">
			Your country:  <input style="border: 1px solid #aaa;" v-model="txt_country"><br>
			Date you intend to send this letter:  <input style="border: 1px solid #aaa;" v-model="txt_date"><br>
			Infringer's name:  <input style="border: 1px solid #aaa;" v-model="txt_name"><br>
			Your name:  <input style="border: 1px solid #aaa;" v-model="txt_ownersname"><br>
			Type of your design:  <input style="border: 1px solid #aaa;" v-model="txt_designtype"><br>
			Name of your design:  <input style="border: 1px solid #aaa;" v-model="txt_designname">
		</div>
		<button id="cease-desist-button" v-on:click="state_letter_generated = true">Generate</button>
		<div id="ceaseselectall" v-show="state_letter_generated" onclick="selectText('ceaseselectall')" 
		style="border:1px solid grey;background-color:#e0e0e0;border-radius:3px;padding:5px 15px 30px 15px;margin:30px 0px 0px 0px;">
			<h4>CEASE AND DESIST DEMAND</h4>
			<br>
			In accordance with the copyright law of {{ txt_country }} and International Copyright Standards
			<br>
			<br>
			{{ txt_date }}
			<br>
			<br>
			Dear {{ txt_name }}:
			<br>
			<br>
			This communication details a cease and desist notice by {{ txt_ownersname }}. If you are represented by legal counsel, please direct this letter to 
			your attorney immediately and have your attorney notify me of such representation. I am writing to notify you that your unlawful copying of my {{ txt_designtype }}: 
			'{{ txt_designname }}' infringes upon my exclusive copyrights. Accordingly, you are hereby directed to
			<br>
			<br>
			CEASE AND DESIST ALL COPYRIGHT INFRINGEMENT.
			<br>
			<br>
			I am the owner of a copyright in various aspects of {{ txt_designname }}. Under {{ txt_country }} copyright law, my copyrights have been in effect since the date 
			that {{ txt_designname }} was created. All copyrightable aspects of {{ txt_designname }} are copyrighted under {{ txt_country }} copyright law.
			<br>
			<br>
			It has come to my attention that you have been copying {{ txt_designname }}. I have copies of your unlawful copies to preserve as evidence. Your actions constitute 
			copyright infringement in violation of {{ txt_country }}'s copyright laws. The consequences of copyright infringement can include statutory damages greater than $1000 
			per work, and damages greater than $10,000 per work for willful infringement. If you continue to engage in copyright infringement after receiving this letter, your 
			actions will be evidence of "willful infringement." Based upon the foregoing, I demand that you immediately (i) cease and desist your unlawful duplication of 
			{{ txt_designname }} and (ii) promptly communicate your assurance within ten (10) days that you will cease and desist from further infringement of my copyrighted works.
			<br>
			<br>
			If you do not comply with this cease and desist demand within this time period, I am entitled to use your failure to comply as evidence of "willful infringement" 
			and seek monetary damages and equitable relief for your copyright infringement. In the event you fail to meet this demand, please be advised that I will contemplate 
			pursuing all available legal remedies, including seeking monetary damages, injunctive relief, and an order that you pay court costs and attorney's fees. Your 
			liability and exposure under such legal action could be considerable.
			<br>
			<br>
			Before taking these steps, however, I wish to give you one opportunity to discontinue your illegal conduct by complying with this demand within ten (10) days. 
			Accordingly, please reply to this email within ten (10) days with your acceptance of the attached Agreement.
			<br>
			<br>
			The foregoing is without waiver of any and all rights of myself, all of which are expressly reserved herein. If you or your attorney have any questions, please 
			contact me directly.
			<br>
			<br>
			Sincerely,
			<br>
			<br>
			{{ txt_ownersname }}
			<br>
			<br>
			<br>
			<br>
			################# Attachment: #################
			<br>
			<br>
			<br>
			<br>
			Copyright Infringement Settlement Agreement
			<br>
			<br>
			I, ___________________, agree to immediately cease and desist copying {{ txt_designname }} in exchange for {{ txt_ownersname }} releasing any and all claims against me for 
			copyright infringement. In the event this agreement is breached by me, {{ txt_ownersname }} will be entitled to costs and attorney's fees in any action brought to enforce 
			this agreement and shall be free to pursue all rights that {{ txt_ownersname }} had as of the date of this letter as if this letter had never been signed.
			<br>
			<br>
			Signed:________________________________
			<br>
			<br>
			Dated:________________________________


		</div>

	<p style="margin-top: 40px;"><span v-on:click="cease" class="yellow_link">Close</span></p>
	
	</div>
</template>



<svg style="display: none">
  <defs>
    <symbol id="icon-visa" viewBox="0 0 40 24">
      <title>Visa</title>
      <path d="M0 1.927C0 .863.892 0 1.992 0h36.016C39.108 0 40 .863 40 1.927v20.146C40 23.137 39.108 24 38.008 24H1.992C.892 24 0 23.137 0 22.073V1.927z" style="fill: #FFF"></path>
      <path d="M0 22.033C0 23.12.892 24 1.992 24h36.016c1.1 0 1.992-.88 1.992-1.967V20.08H0v1.953z" style="fill: #F8B600"></path>
      <path d="M0 3.92h40V1.967C40 .88 39.108 0 38.008 0H1.992C.892 0 0 .88 0 1.967V3.92zM19.596 7.885l-2.11 9.478H14.93l2.11-9.478h2.554zm10.743 6.12l1.343-3.56.773 3.56H30.34zm2.85 3.358h2.36l-2.063-9.478H31.31c-.492 0-.905.274-1.088.695l-3.832 8.783h2.682l.532-1.415h3.276l.31 1.415zm-6.667-3.094c.01-2.502-3.6-2.64-3.577-3.76.008-.338.345-.7 1.083-.793.365-.045 1.373-.08 2.517.425l.448-2.01c-.615-.214-1.405-.42-2.39-.42-2.523 0-4.3 1.288-4.313 3.133-.016 1.364 1.268 2.125 2.234 2.58.996.464 1.33.762 1.325 1.177-.006.636-.793.918-1.526.928-1.285.02-2.03-.333-2.623-.6l-.462 2.08c.598.262 1.7.49 2.84.502 2.682 0 4.437-1.273 4.445-3.243zM15.948 7.884l-4.138 9.478h-2.7L7.076 9.8c-.123-.466-.23-.637-.606-.834-.615-.32-1.63-.62-2.52-.806l.06-.275h4.345c.554 0 1.052.354 1.178.966l1.076 5.486 2.655-6.45h2.683z" style="fill: #1A1F71"></path>
    </symbol>

    <symbol id="icon-master-card" viewBox="0 0 40 24">
      <title>MasterCard</title>
      <path d="M0 1.927C0 .863.892 0 1.992 0h36.016C39.108 0 40 .863 40 1.927v20.146C40 23.137 39.108 24 38.008 24H1.992C.892 24 0 23.137 0 22.073V1.927z" style="fill: #FFF"></path>
      <path d="M11.085 22.2v-1.36c0-.522-.318-.863-.864-.863-.272 0-.568.09-.773.386-.16-.25-.386-.386-.727-.386-.228 0-.455.068-.637.318v-.272h-.478V22.2h.478v-1.202c0-.386.204-.567.523-.567.318 0 .478.205.478.568V22.2h.477v-1.202c0-.386.23-.567.524-.567.32 0 .478.205.478.568V22.2h.523zm7.075-2.177h-.774v-.658h-.478v.658h-.432v.43h.432v.998c0 .5.205.795.75.795.206 0 .433-.068.592-.16l-.136-.407c-.136.09-.296.114-.41.114-.227 0-.318-.137-.318-.363v-.976h.774v-.43zm4.048-.046c-.273 0-.454.136-.568.318v-.272h-.478V22.2h.478v-1.225c0-.363.16-.567.455-.567.09 0 .204.023.295.046l.137-.454c-.09-.023-.228-.023-.32-.023zm-6.118.227c-.228-.16-.546-.227-.888-.227-.546 0-.91.272-.91.703 0 .363.274.567.75.635l.23.023c.25.045.385.113.385.227 0 .16-.182.272-.5.272-.32 0-.57-.113-.728-.227l-.228.363c.25.18.59.272.932.272.637 0 1-.295 1-.703 0-.385-.295-.59-.75-.658l-.227-.022c-.205-.023-.364-.068-.364-.204 0-.16.16-.25.41-.25.272 0 .545.114.682.182l.205-.386zm12.692-.227c-.273 0-.455.136-.568.318v-.272h-.478V22.2h.478v-1.225c0-.363.16-.567.455-.567.09 0 .203.023.294.046L29.1 20c-.09-.023-.227-.023-.318-.023zm-6.096 1.134c0 .66.455 1.135 1.16 1.135.32 0 .546-.068.774-.25l-.228-.385c-.182.136-.364.204-.57.204-.385 0-.658-.272-.658-.703 0-.407.273-.68.66-.702.204 0 .386.068.568.204l.228-.385c-.228-.182-.455-.25-.774-.25-.705 0-1.16.477-1.16 1.134zm4.413 0v-1.087h-.48v.272c-.158-.204-.385-.318-.68-.318-.615 0-1.093.477-1.093 1.134 0 .66.478 1.135 1.092 1.135.317 0 .545-.113.68-.317v.272h.48v-1.09zm-1.753 0c0-.384.25-.702.66-.702.387 0 .66.295.66.703 0 .387-.273.704-.66.704-.41-.022-.66-.317-.66-.703zm-5.71-1.133c-.636 0-1.09.454-1.09 1.134 0 .682.454 1.135 1.114 1.135.32 0 .638-.09.888-.295l-.228-.34c-.18.136-.41.227-.636.227-.296 0-.592-.136-.66-.522h1.615v-.18c.022-.704-.388-1.158-1.002-1.158zm0 .41c.297 0 .502.18.547.52h-1.137c.045-.295.25-.52.59-.52zm11.852.724v-1.95h-.48v1.135c-.158-.204-.385-.318-.68-.318-.615 0-1.093.477-1.093 1.134 0 .66.478 1.135 1.092 1.135.318 0 .545-.113.68-.317v.272h.48v-1.09zm-1.752 0c0-.384.25-.702.66-.702.386 0 .66.295.66.703 0 .387-.274.704-.66.704-.41-.022-.66-.317-.66-.703zm-15.97 0v-1.087h-.476v.272c-.16-.204-.387-.318-.683-.318-.615 0-1.093.477-1.093 1.134 0 .66.478 1.135 1.092 1.135.318 0 .545-.113.682-.317v.272h.477v-1.09zm-1.773 0c0-.384.25-.702.66-.702.386 0 .66.295.66.703 0 .387-.274.704-.66.704-.41-.022-.66-.317-.66-.703z" style="fill: #000"></path>
      <path style="fill: #FF5F00" d="M23.095 3.49H15.93v12.836h7.165"></path>
      <path d="M16.382 9.91c0-2.61 1.23-4.922 3.117-6.42-1.39-1.087-3.14-1.745-5.05-1.745-4.528 0-8.19 3.65-8.19 8.164 0 4.51 3.662 8.162 8.19 8.162 1.91 0 3.66-.657 5.05-1.746-1.89-1.474-3.118-3.81-3.118-6.417z" style="fill: #EB001B"></path>
      <path d="M32.76 9.91c0 4.51-3.664 8.162-8.19 8.162-1.91 0-3.662-.657-5.05-1.746 1.91-1.496 3.116-3.81 3.116-6.417 0-2.61-1.228-4.922-3.116-6.42 1.388-1.087 3.14-1.745 5.05-1.745 4.526 0 8.19 3.674 8.19 8.164z" style="fill: #F79E1B"></path>
    </symbol>

    <symbol id="icon-maestro" viewBox="0 0 40 24">
      <title>Maestro</title>
      <path d="M38.333 24H1.667C.75 24 0 23.28 0 22.4V1.6C0 .72.75 0 1.667 0h36.666C39.25 0 40 .72 40 1.6v20.8c0 .88-.75 1.6-1.667 1.6z" style="fill: #FFF"></path>
      <path d="M14.67 22.39V21c.022-.465-.303-.86-.767-.882h-.116c-.3-.023-.603.14-.788.394-.164-.255-.442-.417-.743-.394-.256-.023-.51.116-.65.324v-.278h-.487v2.203h.487v-1.183c-.046-.278.162-.533.44-.58h.094c.325 0 .488.21.488.58v1.23h.487v-1.23c-.047-.278.162-.556.44-.58h.093c.325 0 .487.21.487.58v1.23l.534-.024zm2.712-1.09v-1.113h-.487v.28c-.162-.21-.417-.326-.695-.326-.65 0-1.16.51-1.16 1.16 0 .65.51 1.16 1.16 1.16.278 0 .533-.117.695-.325v.278h.487V21.3zm-1.786 0c.024-.37.348-.65.72-.626.37.023.65.348.626.72-.023.347-.302.625-.673.625-.372 0-.674-.28-.674-.65-.023-.047-.023-.047 0-.07zm12.085-1.16c.163 0 .325.024.465.094.14.046.278.14.37.255.117.115.186.23.256.37.117.3.117.626 0 .927-.046.14-.138.255-.254.37-.116.117-.232.186-.37.256-.303.116-.65.116-.952 0-.14-.046-.28-.14-.37-.255-.118-.116-.187-.232-.257-.37-.116-.302-.116-.627 0-.928.047-.14.14-.255.256-.37.115-.117.23-.187.37-.256.163-.07.325-.116.488-.093zm0 .465c-.092 0-.185.023-.278.046-.092.024-.162.094-.232.14-.07.07-.116.14-.14.232-.068.185-.068.394 0 .58.024.092.094.162.14.23.07.07.14.117.232.14.186.07.37.07.557 0 .092-.023.16-.092.23-.14.07-.068.117-.138.14-.23.07-.186.07-.395 0-.58-.023-.093-.093-.162-.14-.232-.07-.07-.138-.116-.23-.14-.094-.045-.187-.07-.28-.045zm-7.677.695c0-.695-.44-1.16-1.043-1.16-.65 0-1.16.534-1.137 1.183.023.65.534 1.16 1.183 1.136.325 0 .65-.093.905-.302l-.23-.348c-.187.14-.42.232-.65.232-.326.023-.627-.21-.673-.533h1.646v-.21zm-1.646-.21c.023-.3.278-.532.58-.532.3 0 .556.232.556.533h-1.136zm3.664-.346c-.207-.116-.44-.186-.695-.186-.255 0-.417.093-.417.255 0 .163.162.186.37.21l.233.022c.488.07.766.278.766.672 0 .395-.37.72-1.02.72-.348 0-.673-.094-.95-.28l.23-.37c.21.162.465.232.743.232.324 0 .51-.094.51-.28 0-.115-.117-.185-.395-.23l-.232-.024c-.487-.07-.765-.302-.765-.65 0-.44.37-.718.927-.718.325 0 .627.07.905.232l-.21.394zm2.32-.116h-.788v.997c0 .23.07.37.325.37.14 0 .3-.046.417-.115l.14.417c-.186.116-.395.162-.604.162-.58 0-.765-.302-.765-.812v-1.02h-.44v-.44h.44v-.673h.487v.672h.79v.44zm1.67-.51c.117 0 .233.023.35.07l-.14.463c-.093-.045-.21-.045-.302-.045-.325 0-.464.208-.464.58v1.25h-.487v-2.2h.487v.277c.116-.255.325-.37.557-.394z" style="fill: #000"></path>
      <path style="fill: #7673C0" d="M23.64 3.287h-7.305V16.41h7.306"></path>
      <path d="M16.8 9.848c0-2.55 1.183-4.985 3.2-6.56C16.384.435 11.12 1.06 8.29 4.7 5.435 8.32 6.06 13.58 9.703 16.41c3.038 2.387 7.283 2.387 10.32 0-2.04-1.578-3.223-3.99-3.223-6.562z" style="fill: #EB001B"></path>
      <path d="M33.5 9.848c0 4.613-3.735 8.346-8.35 8.346-1.88 0-3.69-.626-5.15-1.785 3.618-2.83 4.245-8.092 1.415-11.71-.418-.532-.882-.996-1.415-1.413C23.618.437 28.883 1.06 31.736 4.7 32.873 6.163 33.5 7.994 33.5 9.85z" style="fill: #00A1DF"></path>
	</symbol>
	
	<symbol id="icon-american-express" viewBox="0 0 40 24">
      <title>American Express</title>
      <path d="M38.333 24H1.667C.75 24 0 23.28 0 22.4V1.6C0 .72.75 0 1.667 0h36.666C39.25 0 40 .72 40 1.6v20.8c0 .88-.75 1.6-1.667 1.6z" style="fill: #FFF"></path>
      <path style="fill: #1478BE" d="M6.26 12.32h2.313L7.415 9.66M27.353 9.977h-3.738v1.23h3.666v1.384h-3.675v1.385h3.821v1.005c.623-.77 1.33-1.466 2.025-2.235l.707-.77c-.934-1.004-1.87-2.08-2.804-3.075v1.077z"></path>
      <path d="M38.25 7h-5.605l-1.328 1.4L30.072 7H16.984l-1.017 2.416L14.877 7h-9.58L1.25 16.5h4.826l.623-1.556h1.4l.623 1.556H29.99l1.327-1.483 1.328 1.483h5.605l-4.36-4.667L38.25 7zm-17.685 8.1h-1.557V9.883L16.673 15.1h-1.33L13.01 9.883l-.084 5.217H9.73l-.623-1.556h-3.27L5.132 15.1H3.42l2.884-6.772h2.42l2.645 6.233V8.33h2.646l2.107 4.51 1.868-4.51h2.575V15.1zm14.727 0h-2.024l-2.024-2.26-2.023 2.26H22.06V8.328H29.53l1.795 2.177 2.024-2.177h2.025L32.26 11.75l3.032 3.35z" style="fill: #1478BE"></path>
	</symbol>
	
	<symbol id="icon-discover" viewBox="0 0 40 24">
      <title>Discover</title>
      <path d="M38.333 24H1.667C.75 24 0 23.28 0 22.4V1.6C0 .72.75 0 1.667 0h36.666C39.25 0 40 .72 40 1.6v20.8c0 .88-.75 1.6-1.667 1.6z" style="fill: #FFF"></path>
      <path d="M38.995 11.75S27.522 20.1 6.5 23.5h31.495c.552 0 1-.448 1-1V11.75z" style="fill: #F48024"></path>
      <path d="M5.332 11.758c-.338.305-.776.438-1.47.438h-.29V8.55h.29c.694 0 1.115.124 1.47.446.37.33.595.844.595 1.372 0 .53-.224 1.06-.595 1.39zM4.077 7.615H2.5v5.515h1.57c.833 0 1.435-.197 1.963-.637.63-.52 1-1.305 1-2.116 0-1.628-1.214-2.762-2.956-2.762zM7.53 13.13h1.074V7.616H7.53M11.227 9.732c-.645-.24-.834-.397-.834-.695 0-.347.338-.61.8-.61.322 0 .587.132.867.446l.562-.737c-.462-.405-1.015-.612-1.618-.612-.975 0-1.718.678-1.718 1.58 0 .76.346 1.15 1.355 1.513.42.148.635.247.743.314.215.14.322.34.322.57 0 .448-.354.78-.834.78-.51 0-.924-.258-1.17-.736l-.695.67c.495.726 1.09 1.05 1.907 1.05 1.116 0 1.9-.745 1.9-1.812 0-.876-.363-1.273-1.585-1.72zM13.15 10.377c0 1.62 1.27 2.877 2.907 2.877.462 0 .858-.09 1.347-.32v-1.267c-.43.43-.81.604-1.297.604-1.082 0-1.85-.785-1.85-1.9 0-1.06.792-1.895 1.8-1.895.512 0 .9.183 1.347.62V7.83c-.472-.24-.86-.34-1.322-.34-1.627 0-2.932 1.283-2.932 2.887zM25.922 11.32l-1.468-3.705H23.28l2.337 5.656h.578l2.38-5.655H27.41M29.06 13.13h3.046v-.934h-1.973v-1.488h1.9v-.934h-1.9V8.55h1.973v-.935H29.06M34.207 10.154h-.314v-1.67h.33c.67 0 1.034.28 1.034.818 0 .554-.364.852-1.05.852zm2.155-.91c0-1.033-.71-1.628-1.95-1.628H32.82v5.514h1.073v-2.215h.14l1.487 2.215h1.32l-1.733-2.323c.81-.165 1.255-.72 1.255-1.563z" style="fill: #221F20"></path>
      <path d="M23.6 10.377c0 1.62-1.31 2.93-2.927 2.93-1.617.002-2.928-1.31-2.928-2.93s1.31-2.932 2.928-2.932c1.618 0 2.928 1.312 2.928 2.932z" style="fill: #F48024"></path>
    </symbol>
  </defs>
</svg>



<script src="<?php echo get_stylesheet_directory_uri(); ?>/app-scripts.js?v=062"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/app-scripts-404.js?v=062"></script>

<script src="<?php echo get_stylesheet_directory_uri(); ?>/includes/sha256.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/includes/lib-typedarrays-min.js"></script>

</body>
</html>