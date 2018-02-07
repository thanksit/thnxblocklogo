<div class="footer_logo_contact_info col-sm-3">
	{if isset($thnxlogo_img)}
	<div id="footer_logo" class="footer_block footer_logo">
		<div class="block_content">
			<div class="logo">
				<a href="{$base_dir}" title="{if isset($thnxlogo_desc)}{$thnxlogo_desc|escape:'htmlall':'UTF-8'}{/if}">
					<img class="img-responsive" src="{$thnxlogo_img|escape:'htmlall':'UTF-8'}" alt="" title=""/>
				</a>
			</div>
			{if ($thnxlogo_desc !='')}
			<div class="logo_desc">
				{$thnxlogo_desc}
			</div>
			{/if}
		</div>
	</div>
	{/if}
	{hook h="displayLogoContact"}
</div> <!-- footer_logo_contact_info -->