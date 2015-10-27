<form method="POST">
	<input type="hidden" name="action" value="report" />
	<table>
		<tr>
			<td>
				<label for="startDate">Starting Date for Report: <input name="startDate" type="date" class="datepicker" value="{startDate}" />
			</td>
			<td>
				<label for="endDate">End Date for Report: <input name="endDate" type="date" class="datepicker" value="{endDate}" />
			</td>
		</tr>
	</table>
	<input name="GetReport" type="Submit" value="Get Report" />
</form>