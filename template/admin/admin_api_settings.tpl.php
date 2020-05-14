<div class="container-fluid m-l-r s-w">
	 <div class="row">
	 	<?php 
	 	
	 	if(!empty($result_test)) { ?>

			<?php if($result_test['error']) { ?>
			<div class="alert alert-warning smso-alert alert-dismissible show" role="alert">
				<strong>Error!</strong> <?php echo $result_test['message']; ?>
				<a href="<?php echo admin_url('admin.php?page=smso&close-message=true'); ?>" type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</a>
			</div>
			<?php } ?>

			<?php if(!$result_test['error']) { ?>
			<div class="alert alert-warning smso-alert smso-alert-success alert-dismissible show" role="alert">
				<?php echo $result_test['message']; ?>
				<a href="<?php echo admin_url('admin.php?page=smso&close-message=true'); ?>" type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</a>
			</div>
			<?php } ?>

		<?php } ?>
	 	<div class="s-h">	 		 
	 		<img class="smso-h-l" src="/wp-content/plugins/smso/admin/img/logo.png" alt="SMSO">
	 	</div>
	 	<div class="s-b">
	 		 <div class="group">
	 		 	<label><p>Steps</p></label>
	 		 	<ul>
	 		 		<li><span class="tag">1</span><p>Create an account on : <span><a href="https://app.smso.ro/register">SMSO</a></span></p></li>
	 		 		<li><span class="tag">2</span><p>Add credit to your SMSO account</p></li>
	 		 		<li><span class="tag">3</span><p>Get Token for access from here : <a href="https://app.smso.ro/developers/api">Token</a></p></li>
	 		 		<li><span class="tag">4</span><p>Configure settings below</p></li>
	 		 	</ul>
	 		 </div>
	 	</div>
	 </div>
	 <?php if(!empty($this->smso_token) && !empty($this->smso_sender)){ ?>
 	 <div class="row">
 	 	<form action="<?php echo admin_url('admin.php?page=smso'); ?>" method="POST">
			<div class="group">
				<label>Your Phone Number For Testing</label>
				<input type="text" name="smso_phone_number" value="<?php if(!empty(get_option('smso_phone_number'))){ echo get_option('smso_phone_number'); }?>" placeholder="Add your phone number">
				 <div class="description">
					<p>Test you token and selected sender from list.</p>
				</div>
				<input type="submit" name="smso_test" value="SEND TEST">			 
			</div>			 
 	 	</form>
 	 </div>
 	<?php } ?>
</div>


<div class="container-fluid m-l-r s-f">
	<div class="row">
		<form action="<?php echo admin_url('admin.php?page=smso'); ?>" method="POST">
			<div class="group">
				<div class="g">
					<label>Activate SMSO</label>						 
					<input type="checkbox" name="smso_active" value="true"
						<?php if((bool)$this->smso_active){?>
							checked="checked"
						<?php } ?>
					>
				</div>				
			</div>
			<div class="group">
				<label>Token</label>
				<input type="text" name="smso_token" value="<?php if(!empty($this->smso_token)){ echo $this->smso_token; } ?>" placeholder="token">
				 <div class="description">
  					<p>GET SMSO TOKEN FOR ACCESS FROM HERE: https://app.smso.ro/developers/api</p>
  				</div>
			</div>
			<div class="group">
				<label>Sender list</label>				 
				<div class="list">
				<?php foreach(Smso_Admin::getValueSender() as $key => $cost) { ?>
					<div class="group-radio">
					<input type="radio" name="smso_sender" value="<?php echo $cost['id']; ?>" <?php if(!empty($this->smso_sender) && (int)$this->smso_sender == (int)$cost['id']){ ?> checked="checked" <?php } ?> >
					<span><?php echo $cost['label']; ?></span>
					</div>
				<?php } ?>  	
				</div>			 
  				<div class="description">
  					<p>Sender list from SMSO</p>
  				</div>
			</div>
			<?php foreach($this->states as $key => $value){ ?>
			<div class="group">
			<label>Order - <?php echo $value['label']; ?></label>
			<input type="checkbox" name="<?php echo $value['active_name']; ?>"
				   <?php if((bool)($this->{$value['active_name']})){ ?> checked="checked" <?php } ?>
			>
			<textarea  name="<?php echo $value['message_name'];?>" value="<?php if(!empty($this->{$value['message_name']})){ echo $this->{$value['message_name']}; }?>"><?php if(!empty($this->{$value['message_name']})){ echo $this->{$value['message_name']}; }?></textarea>
				<div class="description">
					<p>VARIABLES AVAILABLE: {order_number}, {order_date}, {order_total}, {billing_first_name}, {billing_last_name}, {shipping_method}</p>
				</div>
			</div>
			<?php } ?>
			<input type="submit" name="smso_save_settings" value="SAVE">
		</form> 
	</div>
</div>