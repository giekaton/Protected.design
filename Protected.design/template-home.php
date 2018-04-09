<?php /* Template Name: Protected.design - Home */ get_header(); ?>



<div id="container">

	<div id="app" style="margin: 0 auto;max-width:700px;">

		<app-home ref="app_home" v-if="home"></app-home>

		<app-protected-design  ref="app_protected_design" v-if="protected_design"></app-protected-design>

		<app-faq v-if="faq"></app-faq>

		<app-terms v-if="terms"></app-terms>

		<app-privacy v-if="privacy"></app-privacy>

		<app-contacts v-if="contacts"></app-contacts>

		<app-cease v-if="cease"></app-cease>		

		<app-verify ref="app_verify" v-if="verify"></app-verify>

		<app-footer></app-footer>

	</div>

</div>



<template id="app-home-template">
			<div>

				<div class="verify" v-on:click="verify">
					<b>Verify</b>
				</div>

				<div style="text-align: center;margin-top:60px;margin-bottom:10px;">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/logo.svg?v=002" width="190" style="margin-top:10px;margin-bottom:0px;">
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
						<div style="font-size: 17px;">{{ text_file_input }}</div>
							<input type="file" id="file_input" style="display:none;" @change="file_input_event">

					</div>

					<div v-if="state_file_selected" transition="expand" class="dragdropcontainer" style="margin-top: 10px;padding-top:40px;padding-bottom:37px;cursor:default;">
						<div class="pd_hash">
							The unique ID that represents your file:
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
							<div style="margin-bottom:0px;">Visit the page of your design to enable its protection:</div>
							<div>
							<b style="font-size:18px;" v-on:click="generate_pd" v-html="html_linkhash"></b>
							</div>
						</div>
					</div>

					<p style="margin-top:25px;margin-bottom:15px;">
						Your file will not be uploaded to the internet, and its contents will not be seen by anyone. 
						Only the unique ID that represents your file will be generated and then added to the blockchain. 
						If you change your design's file even slightly, the file's ID will also change, and your protection will be lost. 
						That is why keep a copy of the design's file that you are going to protect. 
						Using the same file, you can later verify the protection. All file formats and file sizes are accepted, 
						but if you want to have your design's visual preview, then select png, gif or jpg file format. 
						For more details, please see the <span class="yellow_link" v-on:click="faq">FAQ</span>.
					</p>

					<br>
					<br>
					<br>
					
					<div class="examples">
					<p>
						<b>Examples:</b>
						<a href="783988862857?https://ironwolf.lt/pd/01.png" target="_blank">Logo</a>,
						<a href="5a74b3a81294?https://ironwolf.lt/pd/13.jpg" target="_blank">Book</a>,
						<a href="0605bd6d5f4f?https://ironwolf.lt/pd/03.png" target="_blank">Illustration</a>,
						<a href="0b783ba6f87e?https://ironwolf.lt/pd/04.png" target="_blank">Lettering</a>,
						<a href="0a4eb89e28f6?https://ironwolf.lt/pd/05.png" target="_blank">Poster</a>,
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
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/logo.svg?v=002" width="190" style="margin-top:10px;margin-bottom:0px;">
					</div>
					<!-- <div class="logo">
						<h2 style="margin:0px;"><a href="/" class="plainlink">Protected.design</a></h2>
					</div> -->
					<p style="margin-top:5;">
						<a :href="text_design_url" class="plainlink"><b>{{ text_design_url }}</b></a>
					</p>

				</div>
					<div v-if="preview_image" class="dragdropcontainer_image">
						<div v-bind:class="[loader ? class_preview_image_loader : '', class_preview_image]">
							<img v-if="preview_src != ''" :src="preview_src" class="preview">
							<div v-else style="padding-top:70px;padding-bottom:70px;font-size:17px;color:#ffffff;">File successfully verified. Preview is not available for this file format</div>
						</div>
					</div>


					<div v-if="preview_text" class="dragdropcontainer" id="dragdropcontainer" v-on:click="file_input_click" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragleave="dragLeaveHandler(event);">
						<div class="preview-false">
							<div class="dragdropcontainertext">
								<div v-html=text_file_input></div>
								<input type="file" id="file_input" style="display:none;" @change="file_input_event">
							</div>
						</div>
					</div>


					<div v-if="preview_false" class="dragdropcontainer" id="dragdropcontainer" v-on:click="file_input_click" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragleave="dragLeaveHandler(event);">
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
					<br><br>
					Status: <b>{{text_status}}</b>
					<span v-if="text_status=='Protected'">
					<br>
					Protection date: {{ text_tx_timestamp }}
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
				This transaction has encountered an error, please contact us by email: <a href="&#109;&#x61;i&#x6c;t&#x6f;:&#x69;n&#102;&#x6f;&#64;&#x70;r&#x6f;t&#x65;c&#116;&#x65;&#100;&#x2e;d&#x65;s&#x69;g&#x6e;">info&#64;&#112;&#x72;&#x6f;&#x74;&#x65;&#x63;&#x74;ed.de&#115;&#105;&#x67;&#x6e;</a>
				</div>

				<div v-show="text_status=='Waiting for payment'" >
				It costs 5 USD to protect the design. This design's file ID (and message, if provided) will be permanently added to the Ethereum 
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
						<input class="pd-checkbox" id="agree" type="checkbox">I have read and agree with the <span class="yellow_link" v-on:click="terms">Terms and Conditions</span>
					</div>
					Amount to be paid: <b>5.00 USD</b>
				</div>
				
				<div style="display: inline; float: left; width: 280px;">
					<button id="submit-button" class="protect-button">Protect this design</button>
				</div>
				<div style="display:inline; float: right; width 400px; text-align: right; font-size: 14px;">
					Payments are securely processed by PayPal's <a href="https://braintreepayments.com" target="_blank">Braintree</a> service<br>
					Your connection is protected using <a href="https://www.ssllabs.com/ssltest/analyze.html?d=protected.design&latest" target="_blank">Let's Encrypt</a> https certificate
				</div>
				<div style="clear:both;margin-bottom:50px;">&nbsp;</div>
				</div>

			</div>
</template>





<template id="app-footer-template">
	<div class="app-footer">
		<!-- <div style="margin-bottom: 4px;">
		<a href="https://facebook.com/protected.design" target="_blank" class="plainlink"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/icon_facebook.svg" width="22"></a>&nbsp;
		<a href="https://twitter.com/protecteddesign" target="_blank" class="plainlink"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/icon_twitter.svg" width="22"></a>&nbsp;
		<a href="https://github.com/dziungles/protected.design" target="_blank" class="plainlink"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/icon_github.svg" width="22"></a>
		</div> -->
		<div style="margin-bottom:5px;">
			<a href="https://facebook.com/protected.design" target="_blank">Facebook</a> | 
			<a href="https://twitter.com/protecteddesign" target="_blank">Twitter</a> | 
			<a href="https://github.com/dziungles/protected.design" target="_blank">GitHub</a><br>
		</div>
		<div style="margin-bottom:5px;">
			<span class="yellow_link" v-on:click="terms">Terms & Conditions</span> | 
			<span class="yellow_link" v-on:click="privacy">Privacy Policy</span> | 
			<span class="yellow_link" v-on:click="faq">FAQ</span> | 
			<span class="yellow_link" v-on:click="contacts">Contacts</span>
		</div>
		<div style="margin-top:3px;">Protected.design V.0.1</div>
		<!-- <div style="margin-top:3px;">&#104;i&#64;p&#114;o&#x74;e&#x63;t&#x65;d&#x2e;d&#x65;s&#x69;g&#x6e;</div> -->
	</div>
</template>



<template id="app-verify-template">
	<div class="app-verify">

		<div style="position:fixed;top:22px;right:30px;cursor:pointer;font-size:30px;" v-on:click="verify">&#10005;</div>

		<div class="app-verify-inner">
			<div class="verify-container">

				<div style="text-align: center;margin-top:60px;margin-bottom:10px;">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/logo.svg?v=002" width="190" style="margin-top:10px;margin-bottom:0px;">
					<!-- <div class="logo" style="text-align:center;">
						<h2 style="margin:0px">Protected.design</h2>
					</div> -->
				</div>

				<div class="dragdropcontainer" id="dragdropcontainer" v-on:click="file_input_click" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragleave="dragLeaveHandler(event);">
					<div style="font-size: 17px;">{{ text_file_input }}</div>
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
	paid by Protected.design directly to the Ethereum network.
	<br>
	<br>
	Example: John selects a design file that has the ID 'd4c8a0fe65951c6531017761146d5a716b073e64' and then adds the message '© Jordan Whitfield'. 
	When he adds this design's data to the blockchain, he gets the transaction nr, in this case it is 0x7f78d6890f85639f9d4c66401f6b9e9cb62256266440e146d07a3a8478e8ed90. 
	He can view this transaction on <a href="https://ropsten.etherscan.io/tx/0x7f78d6890f85639f9d4c66401f6b9e9cb62256266440e146d07a3a8478e8ed90" target="_blank">
	Etherscan</a> (or any other Ethereum block explorer). The data of his design was added to the transaction's 'Input data' field, 
	first the desing's file ID and then the message. The messge can be viewed when converted to ASCII mode.
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
	
	Open your protected design's page and generate cease and desist letter. Then send this letter to the person or company which is infringing your
	intellectual property rights.
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
	and the preview is loaded from this stored file. If the user refreshes the page, the local storage clears, and the preview disappears.
	<br><br>
	When the file is added to the link, after the "?" sign, our back-end PHP script fetches the file and generates its hash (ID). It then checks it
	against the particular design's ID, and if they match it then sends image file data back to the front-end to render the preview. Even though the file is 
	fetched by our back-end script, it is never saved on the server. All traces of the file on our server disappear as soon as the session ends. The back-end 
	solution is being used here because the front-end fetching of a remote file is technically not possible because of CORS.
</p>	
<p>
	<h4>My question was not answered here. How can I contact Protected.design?</h4>
	
	You can contact us by email: <a href="&#109;&#x61;i&#x6c;t&#x6f;:&#x69;n&#102;&#x6f;&#64;&#x70;r&#x6f;t&#x65;c&#116;&#x65;&#100;&#x2e;d&#x65;s&#x69;g&#x6e;">info&#64;&#112;&#x72;&#x6f;&#x74;&#x65;&#x63;&#x74;ed.de&#115;&#105;&#x67;&#x6e;</a>
</p>	

<p><span v-on:click="faq" class="yellow_link">Close FAQ</a></p>
</div>
</template>



<template id="app-terms-template">
<div class="app-faq">
	<div style="position:fixed;top:12px;right:20px;cursor:pointer;font-size:30px;" v-on:click="terms">&#10005;</div>
	<p><h3>Terms and Conditions</h3></p>

	<p>
	<h4>1. Terms</h4>
	These Terms of Use constitute a legally binding agreement made between you, whether personally or on behalf of an entity (“you”) and Protected.design (“we,” “us” or “our”), concerning your access to and use of the https://protected.design website as well as any other media form, media channel, mobile website or mobile application related, linked, or otherwise connected thereto (collectively, the “Site”). You agree that by accessing the Site, you have read, understood, and agreed to be bound by all of these Terms of Use. IF YOU DO NOT AGREE WITH ALL OF THESE TERMS OF USE, THEN YOU ARE EXPRESSLY PROHIBITED FROM USING THE SITE AND YOU MUST DISCONTINUE USE IMMEDIATELY.
	<br><br>
	Supplemental terms and conditions or documents that may be posted on the Site from time to time are hereby expressly incorporated herein by reference. We reserve the right, in our sole discretion, to make changes or modifications to these Terms of Use at any time and for any reason. We will alert you about any changes by updating the “Last updated” date of these Terms of Use, and you waive any right to receive specific notice of each such change. It is your responsibility to periodically review these Terms of Use to stay informed of updates. You will be subject to, and will be deemed to have been made aware of and to have accepted, the changes in any revised Terms of Use by your continued use of the Site after the date such revised Terms of Use are posted.
	<br><br>
	The information provided on the Site is not intended for distribution to or use by any person or entity in any jurisdiction or country where such distribution or use would be contrary to law or regulation or which would subject us to any registration requirement within such jurisdiction or country. Accordingly, those persons who choose to access the Site from other locations do so on their own initiative and are solely responsible for compliance with local laws, if and to the extent local laws are applicable.

	<h4>2. Use License</h4>
	Unless otherwise indicated, the Site is our proprietary property and all source code, databases, functionality, software, website designs, audio, video, text, photographs, and graphics on the Site (collectively, the “Content”) and the trademarks, service marks, and logos contained therein (the “Marks”) are owned or controlled by us or licensed to us, and are protected by copyright and trademark laws and various other intellectual property rights and unfair competition laws of the United States, foreign jurisdictions, and international conventions. The Content and the Marks are provided on the Site “AS IS” for your information and personal use only. Except as expressly provided in these Terms of Use, no part of the Site and no Content or Marks may be copied, reproduced, aggregated, republished, uploaded, posted, publicly displayed, encoded, translated, transmitted, distributed, sold, licensed, or otherwise exploited for any commercial purpose whatsoever, without our express prior written permission.
	<br><br>
	Provided that you are eligible to use the Site, you are granted a limited license to access and use the Site and to download or print a copy of any portion of the Content to which you have properly gained access solely for your personal, non-commercial use. We reserve all rights not expressly granted to you in and to the Site, the Content and the Marks.
	<h4>3. Privacy</h4>
	Please review our Privacy Notice, which also governs your visit to our website, to understand our practices.
	<h4>4. Limitations</h4>
	In no event shall Protected.design or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or 
	due to business interruption) arising out of the use or inability to use the materials on Protected.design's website, even if Protected.design or a 
	Protected.design authorized representative has been notified orally or in writing of the possibility of such damage. Because some jurisdictions do not 
	allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.
	<br><br>
	Protected.design provides the interface for the users to store their design hashes on the Ethereum blockchain. The file's hash stored on the blockchain 
	is mathematically based evidence that the file existed prior to a specific date. If the user includes the message with the author's name on the file or 
	in the message field, this can be used as evidence of design ownership to be presented in courts around the globe.
	<br><br>
	Nevertheless, Protected.design does not take any responsibility for the outcome of the intellectual property dispute regarding any files protected using 
	the Protected.design website. Protected.design allows to create a proof of design existence and its ownership, and generate cease and desist letter, 
	but does not guarantee that the intellectual property dispute can be won on this proof only, as there are many unknown factors that can impact the outcome 
	of the dispute one way or another.
	<br><br>
	Protected.design also does not guarantee that the Ethereum blockchain will work indefinitely or that some newly discovered bug or new technology will 
	not make the Ethereum blockchain obsolete. This is not something that can be known or controlled by Protected.design.
	<h4>5. Refunds</h4>
	Given the nature of the digital services provided by Protected.design and considering the several costs involved in creating the Ethereum transaction, 
	we do not offer refunds.
	<br><br>
	In case our main service - adding data to the blockchain - was not fulfilled and it was a direct cause of Protected.design's application malfunctioning, 
	we guarantee that all steps will be taken to ensure that the service will be fulfilled as soon as possible without any additional costs involved for our users. 
	<br><br>
	In any case where your design's data was not successfully added to the blockchain, but the fee was charged, please contact us by email and we will:<br>
	1. Investigate and find a problem why this issue appeared.<br>
	2. Manually broadcast the transaction to the Ethereum network.
	<h4>6. Accuracy of materials</h4>
	The materials appearing on Protected.design website could include technical, typographical, or photographic errors. Protected.design does not warrant 
	that any of the materials on its website are accurate, complete or current. Protected.design may make changes to the materials contained on its website 
	at any time without notice. However Protected.design does not make any commitment to update the materials.
	<h4>7. Links</h4>
	Protected.design has not reviewed all of the sites linked to its website and is not responsible for the contents of any such linked site. The inclusion 
	of any link does not imply endorsement by Protected.design of the site. Use of any such linked website is at the user's own risk.
	<h4>8. Modifications</h4>
	Protected.design may revise these terms of service for its website at any time without notice. By using this website you are agreeing to be bound by 
	the then current version of these terms of service.
	<h4>9. Governing Law</h4>
	These terms and conditions are governed by and construed in accordance with the laws of Lithuania and you irrevocably submit to the exclusive jurisdiction 
	of the courts in that State or location.

</p>

<p><span v-on:click="terms" class="yellow_link">Close</a></p>
</div>
</template>



<template id="app-privacy-template">
<div class="app-faq">
	<div style="position:fixed;top:12px;right:20px;cursor:pointer;font-size:30px;" v-on:click="privacy">&#10005;</div>
	<p><h3>Privacy Policy</h3></p>

<p>
	Last updated: 21, Mar 2018
	<br><br>
	Protected.design operates https://protected.design (the "Site"). This page informs you of our policies regarding the collection, use and disclosure of Personal 
	Information we receive from users of the Site.
	<br><br>
	We use your Personal Information only for providing and improving the Site. By using the Site, you agree to the collection and use of information in accordance 
	with this policy.
	<br><br>
	<h4>Information Collection and Use</h4>
	We do not collect any additional personal information, except the standard Cookies and Log Data listed below.
	<br><br>
	When the user uses the Site, we only collect the file hash and its size in bytes (metadata). This information is publicly available on protected design's page.
	It helps to identify the file, but does not include any personal information or disclose any private contents. 
	<br><br>
	Optionally, the user can provide the message that will be saved in our database, shown on the design's page and included next to the design's hash on the blockchain. 
	We do not control this and take no responsibility for the message's content provided by the user in the design's 'message' field. The user can change or remove the message, 
	while the design's status is 'Pending...'. The update of the message can be done by repeatedly submitting the same design's file.
	<br><br>
	For payment processing, we use PayPal's Braintree Payments service. When processing your payment, we never see your personal or payment details,
	as they are only collected by the Braintree Payments service. For more details, please refer to 
	<a href="https://www.braintreepayments.com/legal/braintree-privacy-policy" target="_blank">Braintree Payments privacy policy</a>.
	<br><br>
	<h4>Log Data</h4>
	Like many site operators, we collect information that your browser sends whenever you visit our Site ("Log Data").
	<br><br>
	This Log Data may include information such as your computer's Internet Protocol ("IP") address, browser type, browser version, the pages of our Site that you visit, the 
	time and date of your visit, the time spent on those pages and other statistics.
	<br><br>
	In addition, we may use third party services such as Google Analytics that collect, monitor and analyze this in order to provide user behavior insights and statistics 
	for our website.
	<br><br>
	<h4>Cookies</h4>
	Cookies are files with small amount of data, which may include an anonymous unique identifier. Cookies are sent to your browser from a web site and stored on your 
	computer's hard drive.
	<br><br>
	Like many sites, we use "cookies" to collect information. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent.
	<br><br>
	<h4>Security</h4>
	The security of your Personal Information is important to us, but remember that no method of transmission over the Internet, or method of electronic storage, 
	is 100% secure. While we strive to use commercially acceptable means to protect your Personal Information, we cannot guarantee its absolute security.
	<br><br>
	<h4>Changes to This Privacy Policy</h4>
	This Privacy Policy is effective as of 21, Mar 2018 and will remain in effect except with respect to any changes in its provisions in the future, which will 
	be in effect immediately after being posted on this page.
	<br><br>
	We reserve the right to update or change our Privacy Policy at any time and you should check this Privacy Policy periodically. Your continued use of the Service 
	after we post any modifications to the Privacy Policy on this page will constitute your acknowledgment of the modifications and your consent to abide and be bound 
	by the modified Privacy Policy.
	<br><br>
	If we make any material changes to this Privacy Policy, we will notify you either through the email address you have provided us, or by placing a prominent notice 
	on our website.
	<br><br>
	<h4>Contact Us</h4>
	If you have any questions about this Privacy Policy, please contact us.


</p>

<p><span v-on:click="privacy" class="yellow_link">Close</a></p>
</div>
</template>



<template id="app-contacts-template">
<div class="app-faq">
	<div style="position:fixed;top:12px;right:20px;cursor:pointer;font-size:30px;" v-on:click="contacts">&#10005;</div>
	<p><h3>Contacts</h3></p>

<p>
	If you have any questions, please contact us by email: <b><a href="&#109;&#x61;i&#x6c;t&#x6f;:&#x69;n&#102;&#x6f;&#64;&#x70;r&#x6f;t&#x65;c&#116;&#x65;&#100;&#x2e;d&#x65;s&#x69;g&#x6e;">info&#64;&#112;&#x72;&#x6f;&#x74;&#x65;&#x63;&#x74;ed.de&#115;&#105;&#x67;&#x6e;</a></b>
	<br>
	<br>
	<br>
	Social:<br>
	<a href="https://facebook.com/protected.design" target="_blank">Facebook</a><br> 
	<a href="https://twitter.com/protecteddesign" target="_blank">Twitter</a><br>
	<a href="https://github.com/dziungles/protected.design" target="_blank">GitHub</a><br>
	<br>
	<br>
	<br>
</p>
<br>
<p><span v-on:click="contacts" class="yellow_link">Close</a></p>
</div>
</template>



<template id="app-cease-template">
<div class="app-faq">
	<div style="position:fixed;top:12px;right:20px;cursor:pointer;font-size:30px;" v-on:click="cease">&#10005;</div>
	<p><h3>Cease and desist letter generator</h3></p>
	
	If you found out about the infringement of your intellectual property, please use this page to generate a cease and desist letter. 
	Then send this letter to the person or company which is infringing your intellectual property rights.
	<br><br>
	If the person or company does not collaborate, then send this letter to the authorities, e.g., the hosting provider of the website
	or use the copyright infringement form to contact Facebook, YouTube or any other web platform if the intellectual property infringement is on any of these sites.
	The illegal use of your intellectual property will then be taken down by the authorities.
	<br><br>
	<b>Provide details for your cease and desist letter:</b><br><br>
	<div style="line-height:30px;margin-bottom:5px;">
		Your country:  <input v-model="txt_country"><br>
		Date you intend to send this letter:  <input v-model="txt_date"><br>
		Infringer's name:  <input v-model="txt_name"><br>
		Your name:  <input v-model="txt_ownersname"><br>
		Type of your design:  <input v-model="txt_designtype"><br>
		Name of your design:  <input v-model="txt_designname">
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
		'{{ txt_designname }}'' infringes upon my exclusive copyrights. Accordingly, you are hereby directed to
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

<p style="margin-top: 40px;"><span v-on:click="cease" class="yellow_link">Close</a>
</div>
</template>



<script src="<?php echo get_stylesheet_directory_uri(); ?>/app-scripts.js?v=032"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/app-scripts-404.js?v=032"></script>

<script src="<?php echo get_stylesheet_directory_uri(); ?>/includes/sha256.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/includes/lib-typedarrays-min.js"></script>

</body>
</html>