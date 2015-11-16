<form action="index.php" method="POST">
	<input type="hidden" name="action" value="printAttempts" />
	<input type="hidden" name="SubmissionID" value="{SubmissionID}" />
	<input type="hidden" name="AttemptID" value="{AttemptID}" />
	<fieldset>
		<legend>{legend}</legend>
		<table>
			<tr>
				<td>
					<label for="Started">When was the print attempt started: </label>
				</td>
				<td>
					<input type="hidden" name="Started" id="Started" value="{Started}" />
					<p>{Started}</p>
				</td>
			</tr>
			<tr>
				<td>
					<label for="Machine">Which printer is being used: </label>
				</td>
				<td>
					<input type="text" name="Machine" id="Machine" value="{Machine}" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="color">What color filament is being used: </label>
				</td>
				<td>
					<select name="color" id="color">
						{color_options}
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="Grams">How much material (in Grams) did/will the print use: </label>
				</td>
				<td>
					<input type="text" name="Grams" id="Grams" value="{Grams}" />
				</td>
			</tr>
			<tr>
				<td>
					<p>How long did the print take: </p>
				</td>
				<td>
					<label for="Hours" style="font-size:90%;font-style:italic;color:gray">Hours</label>
					<input type="text" name="Hours" id="Hours" value="{Hours}" />
					<label for="Minutes" style="font-size:90%;font-style:italic;color:gray">Minutes</label>
					<input type="text" name="Minutes" id="Minutes" value="{Minutes}" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="successful">Was the print successful: </label>
				</td>
				<td>
					<input type="checkbox" name="successful" id="successful" {checked_succesful} value="1" />
				</td>
			</tr>
		</table>
	</fieldset>
	<input type="submit" name="{FormAction}" value="Save Attempt Information" />
</form>