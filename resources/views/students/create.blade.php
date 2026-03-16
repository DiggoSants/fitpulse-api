<h1>Novo Student</h1>

<form action="/students" method="POST">
@csrf

<input type="number" name="user_id" placeholder="User ID">

<input type="number" name="instructor_id" placeholder="Instructor ID">

<input type="text" name="biometric_id" placeholder="Biometric ID">

<input type="text" name="rfid_tag" placeholder="RFID Tag">

<input type="date" name="birth_date">

<select name="is_defaulter">
<option value="0">Não</option>
<option value="1">Sim</option>
</select>

<button type="submit">Salvar</button>

</form>