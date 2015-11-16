<form action="index.php" method="POST" >
	<input type="hidden" name="AttemptID" value="{id}" />
	<input type="hidden" name="SubmissionID" value="{3dprinting_id}" />
	<input type="hidden" name="action" value="printAttempts" />
	<fieldset>
		<legend>Print Attempt Information</legend>
		<table>
			<tr>
				<td>Attempt Started:</td>
				<td>{Started}</td>
			</tr>
			<tr>
				<td>Which Machine: </td>
				<td>{Machine}</td>
			</tr>
			<tr>
				<td>Color:</td>
				<td>{color}</td>
			</tr>
			<tr>
				<td>Was Print Successful:</td>
				<td>{successful_text}</td>
			</tr>
		</table>
	</fieldset>
	<input type="submit" name="EditAttemptForm" value="Edit" />
</form>