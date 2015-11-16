<form action="index.php" method="POST">
	
	<input type="hidden" name="action" value="PatronInfo" />
	<input type="hidden" name="id" value="{ID}" />
	<fieldset>
		<legend>Patron Information</legend>
		<table>
			<tr>
				<td>
					<label for="patron_name">Patron Name:</label>
				</td>
				<td>
					<input type="text" id="patron_name" name="patron_name" value="{patron_name}" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="library_card">Library Card Number:</label>
				</td>
				<td>
					<input type="text" id="library_card" name="library_card" value="{library_card}" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="phone">Phone Number:</label>
				</td>
				<td>
					<input type="text" id="phone" name="phone" value="{phone}" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="email">E-Mail Address:</label>
				</td>
				<td>
					<input type="text" id="email" name="email" value="{email}" />
				</td>
			</tr>
		</table>
	</fieldset>
	<input type="submit" name="{action_name}" value="Save" />
</form>
					