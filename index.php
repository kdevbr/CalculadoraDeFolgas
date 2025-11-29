<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Folgas</title>
</head>
<body>
    <h1>Próximas folgas (5 por 1) a partir de 21/11:</h1>
    <ul id="offDaysList"></ul>

    <h2>Verificar data</h2>
    <label for="inputDate">Insira uma data (dd/mm):</label>
    <input type="text" id="inputDate" placeholder="03/12">
    <button id="checkDateButton">Verificar</button>
    <p id="result"></p>

    <script>
        // Função para calcular as próximas folgas
        function calculateOffDays(startDate, intervalWorkDays, intervalOffDays, totalDays = 30) {
            const offDays = [];
            const currentDate = new Date(startDate);

            for (let i = 0; i < totalDays; i++) {
                if ((i % (intervalWorkDays + intervalOffDays)) >= intervalWorkDays) {
                    offDays.push(new Date(currentDate));
                }
                currentDate.setDate(currentDate.getDate() + 1);
            }

            return offDays;
        }

        // Função para verificar entre quais folgas uma data está
        function findBetweenOffDays(date, offDays) {
            for (let i = 0; i < offDays.length - 1; i++) {
                if (date >= offDays[i] && date <= offDays[i + 1]) {
                    return { before: offDays[i], after: offDays[i + 1] };
                }
            }
            return null;
        }

        // Data inicial e intervalos
        const startDate = new Date();
        startDate.setDate(21);
        startDate.setMonth(10); // Novembro (mês é zero-based em JS)
        const intervalWorkDays = 5;
        const intervalOffDays = 1;

        // Calcula as próximas folgas
        let offDays = calculateOffDays(startDate, intervalWorkDays, intervalOffDays);

        // Exibe as folgas na página
        const offDaysList = document.getElementById('offDaysList');
        offDays.forEach(day => {
            const listItem = document.createElement('li');
            listItem.textContent = day.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
            offDaysList.appendChild(listItem);
        });

        // Verifica entre quais folgas uma data está
        const checkDateButton = document.getElementById('checkDateButton');
        checkDateButton.addEventListener('click', () => {
            const inputDate = document.getElementById('inputDate').value;
            const [day, month] = inputDate.split('/').map(Number);
            const userDate = new Date(startDate.getFullYear(), month - 1, day);
            const timeDifference = Math.abs(userDate - startDate);
            const dayDifference = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));
            alert(`Diferença em dias: ${dayDifference}`);
            offDays = calculateOffDays(startDate, intervalWorkDays, intervalOffDays, dayDifference+7);

        offDaysList.innerHTML = ''; // Limpa a lista antes de adicionar novos itens
        offDays.forEach(day => {
            const listItem = document.createElement('li');
            listItem.textContent = day.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
            offDaysList.appendChild(listItem);
        });

            const result = findBetweenOffDays(userDate, offDays);
            const resultElement = document.getElementById('result');

            if (result) {
                resultElement.textContent = `Entre as folgas ${result.before.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' })} e ${result.after.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' })}`;
            } else {
                resultElement.textContent = 'A data não está entre duas folgas calculadas.';
            }
        });
    </script>
</body>
</html>