<tr>
	<{cellType}>{submitted}</{cellType}>
	<{cellType}>{patron_name}<br /><a href="{PatronInfo_url}">{PatronInfo_text}</a></{cellType}>
	<{cellType}>{library_card}</{cellType}>
	<{cellType}>{phone}</{cellType}>
	<{cellType}><a href="mailto:{email}">{email}</a></{cellType}>
	<{cellType}>{tosAgreement}</{cellType}>
	
	<{cellType}><p>{printed}</p>
	<p><a href="{printed_url}">{printed_text}</a></p>
	</{cellType}>
	<{cellType}>{contacted}</{cellType}>
	<{cellType}>{picked_up}</{cellType}>
	<{cellType}>
		<p>
			<a href="{final_location}">Download File</a>
		</p>
		<p>
			<a href="{PrintInfo_url}">{PrintInfo_text}</a>
		</p>
	</{cellType}>
	<{cellType}>{Color}</{cellType}>
</tr>
	