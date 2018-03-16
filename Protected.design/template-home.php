<?php /* Template Name: Protected.design - Home */ get_header(); ?>



<div id="container">

	<div id="app" style="margin: 0 auto;max-width:700px;">

		<app-home ref="app_home" v-if="home"></app-home>

		<app-protected-design  ref="app_protected_design" v-if="protected_design"></app-protected-design>

		<app-faq v-if="faq"></app-faq>

		<app-terms v-if="terms"></app-terms>

		<app-cease v-if="cease"></app-cease>		

		<app-verify ref="app_verify" v-if="verify"></app-verify>

		<app-footer></app-footer>

	</div>

</div>



<template id="app-home-template">
			<div>

				<div class="verify" v-on:click="verify">
					Verify
				</div>

				<div style="text-align: center;margin-top:60px;margin-bottom:10px;">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/logo.svg" width="190" style="margin-top:10px;margin-bottom:0px;">
					<!-- <div class="logo">
						<h2 style="margin:0px">Protected.design</h2>
					</div> -->

				</div>

				<p>
					Using the Ethereum blockchain, Protected.design allows you to create a legal proof that you 
					are the owner of the design and that the design existed prior to a specific date.
				</p>
				<br>
				<br>
				<div class="template-content">
	
					<div class="dragdropcontainer" id="dragdropcontainer" v-on:click="file_input_click" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragleave="dragLeaveHandler(event);">
						<div style="font-size: 18px;">{{ text_file_input }}</div>
							<input type="file" id="file_input" style="display:none;" @change="file_input_event">

					</div>

					<div v-if="state_file_selected" transition="expand" class="dragdropcontainer" style="margin-top: 10px;cursor:default;">
						<div class="pd_hash">
							The unique ID that represents your file. It will be added to the blockchain:
							<br>
							<b style="font-size:16px;">{{ text_hash }}</b>
							<br><br>
							Optionally, you can include a text message (e.g. author's name, max. 64 ASCII characters) that will be addded to the blockchain next to your design's ID:<br>
							<input class="message" id="pd_message" type="text" maxlength="64" @click="restrict_characters">
							<span v-show="text_input_error.length > 0" style="color:red;font-size:14px;">
							<br>
							{{ text_input_error }}
							</span>
							<br>
							<br>
							Visit the page of your design to enable its protection:<br>
							<b style="font-size:18px;cursor:pointer;" v-on:click="generate_pd" v-html="html_linkhash"></b>
						</div>
					</div>

					<p style="margin-top:25px;margin-bottom:15px;">
						Your file will not be uploaded to the internet and its contents will not be seen by anyone. 
						Only the unique ID that represents your file will be generated and then added to the blockchain. 
						If you change your design's file even slightly, the file's ID will also change, and your protection will be lost. 
						That is why keep a copy of the design's file that you are going to protect. 
						Using the same file, you can later verify the protection. All file formats and file sizes are accepted, 
						but if you want to have your design's visual preview, then select png, gif or jpg file format. 
						For more details, see the <span class="yellow_link" v-on:click="faq">FAQ</a>.
					</p>

					<br>
					<br>
					<br>
					
					<div style="border: 1px solid grey;padding-left:10px;padding-right:10px;">
					<p>
						<b>Examples:</b>
						<a href="783988862857?https://ironwolf.lt/pd/01.png" target="_blank">Logo</a>,
						<a href="5a74b3a81294?https://ironwolf.lt/pd/13.jpg" target="_blank">Book</a>,
						<a href="0605bd6d5f4f?https://ironwolf.lt/pd/03.png" target="_blank">Illustration</a>,
						<a href="0b783ba6f87e?https://ironwolf.lt/pd/04.png" target="_blank">Lettering</a>,
						<a href="0a4eb89e28f6?https://ironwolf.lt/pd/05.png" target="_blank">Poster</a>
						<a href="1c6b3ca954d1?https://ironwolf.lt/pd/10.png" target="_blank">Game</a>,
						<a href="31eb6f5535d2?https://ironwolf.lt/pd/09.jpg" target="_blank">Statuette</a>,
						<a href="2643099368a3?https://ironwolf.lt/pd/07.jpg" target="_blank">Photo</a>
						<br><br>
						In the examples above, the image preview file is not stored on Protected.design servers, but provided 
						as an external resource after the '?' sign in the URL. 
						For more details, please see the <span class="yellow_link" v-on:click="faq">FAQ</span>.
					</p>
					</div>

				</div>

			</div>
</template>



<template id="app-protected-design-template">
			<div>

				<div style="text-align: center;margin-top:60px;margin-bottom:10px;">

					<div style="cursor:pointer;" v-on:click="home">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/logo.svg" width="190" style="margin-top:10px;margin-bottom:0px;">
					</div>
					<!-- <div class="logo">
						<h2 style="margin:0px;"><a href="/" class="plainlink">Protected.design</a></h2>
					</div> -->
					<p style="margin-top:5;">
						<a :href="text_design_url" class="plainlink"><b>{{ text_design_url }}</b></a>
					</p>

				</div>

					<div v-if="preview_image" class="dragdropcontainer_image">
						<div class="preview-image">
							<img v-if="preview_src != ''" :src="preview_src" class="preview">
							<div v-else style="margin-top:50px;font-weight:bold;">File successfully verified. Preview is not available for this file format</div>
						</div>
					</div>


					<div v-if="preview_text" class="dragdropcontainer" id="dragdropcontainer" v-on:click="file_input_click" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragleave="dragLeaveHandler(event);">
						<div class="preview-false">
							<div style="font-size: 18px;padding: 50px;">
								{{ text_file_input }}
								<input type="file" id="file_input" style="display:none;" @change="file_input_event">
							</div>
						</div>
					</div>


					<div v-if="preview_false" class="dragdropcontainer" id="dragdropcontainer" v-on:click="file_input_click" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragleave="dragLeaveHandler(event);">
						<div class="preview-text">
							<div style="font-size: 18px;padding: 50px;">
								{{ text_file_input }}
								<input type="file" id="file_input" style="display:none;" @change="file_input_event">
							</div>
						</div>
					</div>
				

				<p style="text-align:left;">
					The unique ID that represents this design file: <b>{{text_hash}}</b>
					<br>
					File size: {{text_file_size}}
					<br>
					Message: {{text_message_ascii}}
					<br><br>
					Status: <b>{{text_status}}</b>
					<span v-if="text_status=='Protected'">
					<br>
					Protection date: <a :href="text_tx_url" target="_blank">Available on Etherscan</a>
					<br><br>
					This design's file ID has been permanently added to the following transaction's 'Input data' field on the Ethereum blockchain: 
					<a :href="text_tx_url" 
					target="_blank">{{ text_tx_hash }}</a>
					<br><br><br>
					<button id="cease-desist-button" v-on:click="cease">Generate cease and desist letter</button>
					<br><br>
					</span>
				</p>

				<div v-if="text_status=='Pending'" v-bind="check_status()">
				Your transaction is being processed. Please wait...
				</div>

				<div v-if="text_status=='Error'">
				This transaction has encountered an error, please contact us by email: &#104;i&#64;p&#114;o&#x74;e&#x63;t&#x65;d&#x2e;d&#x65;s&#x69;g&#x6e;
				</div>

				<div v-show="text_status=='Waiting for payment'" >
				It costs 5 USD to protect the design. This design's file ID (and optional message, if provided) will be permanently added to the Ethereum 
				transaction's data input field on the blockchain. The 5 USD fee also covers the Ethereum transaction cost. This 
				fee can be paid by the design's author or by anyone else. The timestamp for design protection will be 
				available as soon as the payment is received and the Ethereum transaction is broadcasted to the network.
				<br>
				<br>
				<i style="color:grey;">Testing mode: Card nr.: 4111111111111111, Exp. date: 12/24, CVV: 123.</i>
				<br><br>
				<div id="dropin-container"></div>
				<div style="margin-bottom:10px;">
					<div style="margin-bottom:5px;">
						<input class="pd-checkbox" type="checkbox">I have read and agree with the <span class="yellow_link" v-on:click="terms">terms and conditions</span>
					</div>
					Amount to be paid: <b>5.00 USD</b>
				</div>
				
				<div style="display: inline; float: left; width: 300px;">
					<button id="submit-button" class="protect-button">Protect this design</button>
				</div>
				<div style="display:inline; float: right; width 400px; text-align: right; font-size: 14px;">
					Payments are securely processed by PayPal's <a href="https://braintreepayments.com" target="_blank">Braintree</a> service<br>
					Your connection is protected using <a href="https://www.ssllabs.com/ssltest/analyze.html?d=protected.design&latest" target="_blank">Let's Encrypt</a> https certificate
				</div>
				<div style="clear:both;">&nbsp;</div>
				</div>

			</div>
</template>



<template id="app-footer-template">
	<div class="app-footer">
		<a href="https://facebook.com/protected.design" target="_blank">Facebook</a> | 
		<a href="https://twitter.com/protecteddesign" target="_blank">Twitter</a> | 
		<a href="https://github.com/dziungles/protected.design" target="_blank">GitHub</a>
		<div style="margin-top:3px;">Protected.design V.0.1 on the Ethereum testnet</div>
		<div style="margin-top:3px;">&#104;i&#64;p&#114;o&#x74;e&#x63;t&#x65;d&#x2e;d&#x65;s&#x69;g&#x6e;</div>
	</div>
</template>



<template id="preview-text-template">
	<div class="dragdropcontainer" id="dragdropcontainer" v-on:click="file_input_click" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragleave="dragLeaveHandler(event);">
		<div class="preview-text">
			<div style="font-size: 18px;margin-top:180px;">
				{{ text_file_input }}
				<input type="file" id="file_input" style="display:none;" @change="file_input_event">
			</div>
		</div>
	</div>
</template>



<template id="preview-false-template">
	<div class="dragdropcontainer" id="dragdropcontainer" v-on:click="file_input_click" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragleave="dragLeaveHandler(event);">
		<div class="preview-false">
			<div style="font-size: 18px;margin-top:90px;">
				{{ text_file_input }}
				<input type="file" id="file_input" style="display:none;" @change="file_input_event">
			</div>
		</div>
	</div>
</template>



<template id="preview-image-template">
	<div class="dragdropcontainer_image">
		<div class="preview-image">
			<img :src="preview_src" class="preview">
		</div>
	</div>
</template>



<template id="app-verify-template">
	<div class="app-verify">

		<div style="position:fixed;top:22px;right:30px;cursor:pointer;font-size:30px;" v-on:click="verify">&#10005;</div>

		<div class="app-verify-inner">
			<div class="verify-container">

				<div style="text-align: center;margin-top:60px;margin-bottom:10px;">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/logo.svg" width="190" style="margin-top:10px;margin-bottom:0px;">
					<!-- <div class="logo" style="text-align:center;">
						<h2 style="margin:0px">Protected.design</h2>
					</div> -->
				</div>

				<div class="dragdropcontainer" id="dragdropcontainer" v-on:click="file_input_click" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragleave="dragLeaveHandler(event);">
					<div style="font-size: 18px;">{{ text_file_input }}</div>
						<input type="file" id="file_input" style="display:none;" @change="file_input_event">
					</div>
				<p>
					{{ text_verify_result }}<br>
					<b style="font-size:16px;cursor:pointer;" class="linkhash" v-if="text_verify_link != ''" v-on:click="generate_pd">{{ text_verify_link }}</b>
				</p>
				<br>
			</div>
		</div>
	</div>
</template>



<template id="app-faq-template">
<div class="app-faq">
	<div style="position:fixed;top:12px;right:20px;cursor:pointer;font-size:30px;" v-on:click="faq">&#10005;</div>
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
	paid by Proteted.design directly to the Ethereum network.
	<br>
	<br>
	Example: John selects a design file that has the ID 'd4c8a0fe65951c6531017761146d5a716b073e64' and then adds the message '© Jordan Whitfield'. 
	When he adds this design's data to the blockchain, he gets the transaction nr, in this case it is 0x7f78d6890f85639f9d4c66401f6b9e9cb62256266440e146d07a3a8478e8ed90. 
	He can view this transaction on <a href="https://ropsten.etherscan.io/tx/0x7f78d6890f85639f9d4c66401f6b9e9cb62256266440e146d07a3a8478e8ed90" target="_blank">
	Etherscan</a> (or any other Ethereum block explorer). The data of his design was added to the transaction's 'Input data' field, 
	first the desing's file ID and then the message. The messge can be viewed, when converted to ASCII mode.
	<br>
	<br>
	<img src="https://protected.design/img/pd_example.png">
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
	
	Open your protected design page and generate cease and desist letter. (Soon...)
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
	
	The ID is a <a href="https://en.wikipedia.org/wiki/SHA-2" target="_blank">SHA-256</a> hash truncated to the first 40 characters. 
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
	
	When the user selects a design file from local device, the file is not uploaded to Protected.design but stored locally in browser's cache 
	and the preview is loaded from this stored file. If the user refreshes the page, the local storage clears, and the preview disappears.
	<br><br>
	When the file is added to the link, after the "?" sign, our back-end PHP script fetches the file and generates its hash (ID). It then checks it
	against the particular design's ID, and if they match it then sends image file data back to the front-end to render the preview. Even though the file is 
	fetched by our back-end script, it is never saved on the server. All traces of the file on our server disappear as soon as the session ends. The back-end 
	solution is being used here because the front-end fetching of a remote file is technically not possible because of CORS.
</p>	
<p>
	<h4>My question was not answered here. How can I contact Protected.design?</h4>
	
	You can contact us by email: &#104;i&#64;p&#114;o&#x74;e&#x63;t&#x65;d&#x2e;d&#x65;s&#x69;g&#x6e;
</p>	

<p><span v-on:click="faq" class="yellow_link">Close FAQ</a></p>
</div>
</template>



<template id="app-terms-template">
<div class="app-faq">
	<div style="position:fixed;top:12px;right:20px;cursor:pointer;font-size:30px;" v-on:click="terms">&#10005;</div>
	<p><h3>Terms and Conditions</h3></p>

<p>
	<h4>Soon...</h4>
	
	Soon...

</p>

<p><span v-on:click="terms" class="yellow_link">Close</a></p>
</div>
</template>



<template id="app-cease-template">
<div class="app-faq">
	<div style="position:fixed;top:12px;right:20px;cursor:pointer;font-size:30px;" v-on:click="cease">&#10005;</div>
	<p><h3>Cease and desist letter</h3></p>

<p>
	<h4>Soon...</h4>
	
	Soon...

</p>

<p><span v-on:click="cease" class="yellow_link">Close</a></p>
</div>
</template>



<script src="<?php echo get_stylesheet_directory_uri(); ?>/app-scripts.js?v=019"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/app-scripts-404.js?v=019"></script>

<script src="<?php echo get_stylesheet_directory_uri(); ?>/includes/sha256.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/includes/lib-typedarrays-min.js"></script>

</body>
</html>