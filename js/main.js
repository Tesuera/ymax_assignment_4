function logout () {
    fetch('./_actions/handle_logout.php', {method: 'POST'})
    .then(res => res.json())
    .then(data => {
        if(data.status == 200) {
            location.href = './login.php';
        } 
    })
}