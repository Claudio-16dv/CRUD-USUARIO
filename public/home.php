<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="/css/style.css">
<script>
    async function getUserById() {
    const userId = document.getElementById('iduser').value;

        if (!userId) {
        alert('Por favor, insira um ID de usuário.');
        return;
        }

        const response = await fetch('/user/getUserById', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: userId })
        });

        const result = await response.json();

        if (response.ok) {
        const userDetails = document.getElementById('userDetails');
        userDetails.innerHTML = ''; 

        for (const [key, value] of Object.entries(result)) {
            const listItem = document.createElement('li');
            listItem.innerHTML = `<strong>${key}:</strong> ${value}`;
            userDetails.appendChild(listItem);
        }
        } else {
        alert(result.error);
        }
    }


        async function deleteUserById() {
            const userId = document.getElementById('iduser-delete').value;

            if (!userId) {
                alert('Por favor, insira um ID de usuário.');
                return;
            }

            const response = await fetch('/user/deleteUserById', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: userId })
            });

            const result = await response.json();

            if (response.ok) {
                alert('Usuário deletado com sucesso!');
                document.getElementById('iduser-delete').value = '';
            } else {
                alert(result.error);
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>Bem-vindo!</h1>
    </header>
    <section>
    
        <form id="getUserForm" onsubmit="event.preventDefault(); getUserById();">
            <label for="iduser">Informe o ID do usuário que deseja ver as informações:</label>
            <input type="number" name="id" id="iduser">
            <input type="submit" value="Pesquisar">
        </form>

        
        <div id="userInfo" class="user-info">
         <ul id="userDetails"></ul>
        </div>

        
        <form id="deleteUserForm" onsubmit="event.preventDefault(); deleteUserById();">
            <label for="iduser-delete">Informe o ID do usuário que deseja deletar:</label>
            <input type="number" name="id" id="iduser-delete">
            <input type="submit" value="Deletar">
        </form>

        <input type="button" value="Editar dados" onclick="window.location.href='edit.html'">
    </section>
</body>
</html>