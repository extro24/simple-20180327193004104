<form align=center name="_xclick" target="_blank" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">		<input type="hidden" name="cmd" value="_xclick"><input type="hidden" name="business" value="{$payment->email}"><input type="hidden" name="currency_code" value="USD"><input type="hidden" name="item_name" value="{$price->title}"><input type="hidden" name="amount" value="{$price->price}"><input type="hidden" name="on1" value="UID"><input type="hidden" name="os1" value="{$player->id}"><input type="hidden" name="notify_url" value="{$_PAYMENTS_IPN}"><input type="image" src="templates/images/3rd/paypal.gif" border="0" style="height:20px;" name="submit" title="Pay with paypal"></form>