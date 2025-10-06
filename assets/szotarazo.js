document.addEventListener('DOMContentLoaded', () => {
    const gomb = document.getElementById('szotarazo');
    if (gomb) {
        gomb.addEventListener('click', () => {
            fetch('szotarazo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || 'Szótár frissítve!');
            })
            .catch(err => {
                console.error('Hiba a szótárazás során:', err);
                alert('⚠️ Hiba történt a szótár frissítésekor.');
            });
        });
    }
});
