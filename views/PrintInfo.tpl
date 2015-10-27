<form action="index.php" method="POST">
	<input type="hidden" name="action" value="PrintInfo" />
	<input type="hidden" name="id" value="{ID}" />
	<fieldset>
		<legend>Model Printing Information</legend>
		<table>
			<tr>
				<td>
					<p>File Link</p>
					<p style="font-size:90%;font-style:italic;color:gray">Right click on the link and select "Save link as..."</p>
				</td>
				<td>
					<a href="{final_location}">Download File</a>
				</td>
			</tr>
			<tr>
				<td>
					<label for="color">Filament Color:</label>
				</td>
				<td>
					<select id="Color" name="Color">
						{optionsString}
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="Infill">Infill</label>
				</td>
				<td>
					<input type="text" name="Infill" id="Infill" value="{Infill}" />
				</td>
			</tr>
			<!--
			<tr>
				<td>
					<p>Estimated Print Time</p>
				</td>
				<td>
					<label for="estimated_hours" style="font-size:90%;color:gray">Hours</label>
					<input type="text" name="estimated_hours" id="estimated_hours" value="{estimated_hours}" />
					<label for="estimated_minutes" style="font-size:90%;color:gray">Min</label>
					<input type="text" name="estimated_minutes" id="estimated_minutes" value="{estimated_minutes}" />
				</td>
			</tr>
			-->
			<tr>
				<td>
					<p>ACTUAL Print Time</p>
				</td>
				<td>
					<label for="actual_hours" style="font-size:90%;color:gray">Hours</label>
					<input type="text" name="actual_hours" id="actual_hours" value="{actual_hours}" />
					<label for="actual_minutes" style="font-size:90%;color:gray">Min</label>
					<input type="text" name="actual_minutes" id="actual_minutes" value="{actual_minutes}" />
				</td>
			</tr>
			<tr>
				<td>
					<p>Grams</p>
				</td>
				<td>
					<input type="text" name="Grams" id="Grams" value="{Grams}" />
				</td>
			</tr>
			<tr>
				<td>
					<p>Printed Successfully</p>
				</td>
				<td>
					<input type="checkbox" name="printed" id="printed" value="1" {printCheckbox} >
				</td>
			</tr>
		</table>
	</fieldset>
	<input type="submit" name="{action_name}" value="Save" />
</form>