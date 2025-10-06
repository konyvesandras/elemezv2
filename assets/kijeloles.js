document.addEventListener('DOMContentLoaded', () => {
    const selectedWords = new Set();

    document.querySelectorAll('.word').forEach(el => {
        const word = el.textContent.trim();

        if (el.classList.contains('selected')) {
            selectedWords.add(word);
        }

        el.addEventListener('click', () => {
            el.classList.toggle('selected');
            const isSelected = el.classList.contains('selected');

            if (isSelected) {
                selectedWords.add(word);
            } else {
                selectedWords.delete(word);
            }

            fetch('update_kijeloles.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    word: word,
                    action: isSelected ? 'add' : 'remove'
                })
            });
        });
    });
});
