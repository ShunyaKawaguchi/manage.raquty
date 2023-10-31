document.addEventListener('DOMContentLoaded', function() {
    const links = document.querySelectorAll('[new-tournament-register]');
    
    links.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();

            // ユーザーが「Yes」を選択した場合の処理
            const isConfirmed = confirm('大会を作成しますか？');
            if (isConfirmed) {
                const form = document.createElement('form');
                form.method = 'post';
                form.action = '/components/templates/tournament_menu/register_tournament.php';

                const newTournamentInput = document.createElement('input');
                newTournamentInput.type = 'hidden';
                newTournamentInput.name = 'newtournament';
                newTournamentInput.value = 'newtournament';

                form.appendChild(newTournamentInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});

