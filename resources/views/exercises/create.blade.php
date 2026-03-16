<h1>Criar Exercício</h1>

<form action="/exercises" method="POST">

@csrf

<input type="text" name="name" placeholder="Nome do exercício">

<br><br>

<input type="text" name="muscle_group" placeholder="Grupo muscular">

<br><br>

<textarea name="description" placeholder="Descrição"></textarea>

<br><br>

<button type="submit">Salvar</button>

</form>