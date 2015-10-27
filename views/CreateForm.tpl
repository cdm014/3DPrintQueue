<form  method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="create" />
{debugVar}
<fieldset style="margin-bottom:1em; border:2px groove; padding:1em;">
	<legend>Contact Information:</legend>
	<table>
		
		<tr>
			
			<td>
				<label for="patron_name">Name:</label>
			</td>
			<td>
				<input type="text" name="patron_name" value="{patron_name}" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="library_card">Library Card:</label>
			</td>
			<td>
				<input type="text" name="library_card" value="{library_card}" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="email">e-Mail Address:</label>
			</td>
			<td>
				<input type="email" name="email" value="{email}"/>
			</td>
		<tr>
			<td>
				<label for="phone">Telephone Number:</label>
			</td>
			<td>
				<input type="tel" name="phone" value="{phone}" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="checkbox" value="1" name="tosAgreement" />
				<label for="tosAgreement">I agree that in submitting this file I am fully complying with any and all <a href="http://www.rpl.org/about/policies/3d-printer.html">library 
				rules and terms of service regarding my use of the 3d Printer</a>.</label>
		</tr>
	</table>
	</fieldset>
	<fieldset style="margin-bottom:1em; border:2px groove; padding:1em">
	<legend>Model Information:</legend>
	<table>
		<tr>
			<td>
				<label for="model">File to Upload:</label>
			</td>
			<td>
				<input type="file" id="model" name="model">
			</td>
		</tr>
		<tr>
			<td>
				<label for="color">Preferred Color:</label>
			</td>
			<td>
				<select id="color" name="color">
					<option value="ANY" selected>ANY</option>
					<option>White</option>
					<option>Red</option>
					<option>Orange</option>
					<option>Yellow</option>
					<option>Green</option>
					<option>Blue</option>
					<option>Purple</option>
					<option>Gray</option>
					<option>Black</option>
					
				</select>
		</tr>
<!--
		<tr>
			<td>
				<dl>
					<dt><label for="infill">Infill:</label></dt>
					<dd style="font-size:90%; font-style:italic; color:gray">Infill controls how solid the inside of the object is. Higher values make the object more solid 
					and able to handle more weight, but also increase material usage and print time. Changing this option
					could result in your print being delayed.
					</dd>
				</dl>
			</td>
			<td>
				<select id="infill" name="infill">
					<option selected>5%</option>
					<option>10%</option>
					<option>15%</option>
					<option>20%</option>
					<option>25%</option>
					<option>30%</option>
					<option>35%</option>
					<option>45%</option>
					<option>50%</option>
					<option>75%</option>
					<option>100%</option>
				</select>
			</td>
		</tr>-->
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
	<table>
		<tr>
			<td colspan="2">
				<p>Please note that the <!-- infill and --> color setting allows you to express your preferences but we do not guarantee
				that we will be able to honor your selections. Possible reasons that we may not honor the values selected include
				lack of material, or that the duration of your print would be too long. </p>
			</td>
		</tr>

		<tr>
			<td colspan="2" style="padding:1em;padding-left:0em">
				<input type="submit" name="create" value="submit"/>
			</td>
		</tr>
	</table>
	
</form>
		
			