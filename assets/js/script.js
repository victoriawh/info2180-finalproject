function loadContent() {
    fetch('data.txt') 
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            document.getElementById('content').innerHTML = data;
        })
        .catch(error => console.error('Error fetching data:', error));
}

document.getElementById('loadBtn').addEventListener('click', loadContent);