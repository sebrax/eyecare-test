<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <title>Solicitação de Exames</title>
    <style>
        /* Configurações da página para impressão */
        @page {
            margin: 40px 40px 100px 40px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1f2937;
            font-size: 14px;
            margin: 0;
            background-color: #fff;
            -webkit-font-smoothing: antialiased;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #0f1e52;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        /* Logo em texto */
        .logo-text {
            font-weight: 700;
            font-size: 20px;
            color: #0f1e52;
            text-transform: uppercase;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 2px;
        }

        /* Container das informações à direita */
        .info-container {
            text-align: right;
            min-width: 280px;
            padding: 20px 25px;
            border-radius: 10px;
            line-height: 1.5;
            font-size: 11px;
            color: #374151;
        }

        .info-container>div {
            margin-bottom: 8px;
        }

        .info-container strong {
            color: #0f1e52;
        }

        /* Título principal do grupo */
        h2.print-option-title {
            color: #0f1e52;
            margin-top: 40px;
            margin-bottom: 15px;
            border-bottom: 2px solid #0f1e52;
            padding-bottom: 6px;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        /* Tabela estilizada */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            font-size: 14px;
        }

        th,
        td {
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
        }

        th {
            background-color: #0f1e52;
            color: white;
            font-weight: 600;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        td {
            background-color: #f8fafc;
        }

        .laterality {
            font-style: italic;
            color: #4b5563;
        }

        .comment {
            font-style: italic;
            font-size: 13px;
            color: #6b7280;
        }

        /* Observação */
        p.observation {
            font-style: italic;
            font-size: 13px;
            color: #6b7280;
            margin-top: -10px;
            margin-bottom: 30px;
            padding-left: 8px;
            border-left: 3px solid #0f1e52;
        }

        /* Rodapé fixo */
        footer {
            position: fixed;
            bottom: 20px;
            left: 40px;
            right: 40px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
            border-top: 1px solid #d1d5db;
            padding-top: 12px;
            background-color: #fff;
        }

        /* Assinatura */
        .signature-block {
            margin-top: 40px;
            text-align: center;
        }

        .signature-line {
            display: inline-block;
            width: 280px;
            border-top: 2px solid #0f1e52;
            margin-bottom: 6px;
        }

        .signature-name {
            font-weight: 700;
            color: #0f1e52;
            letter-spacing: 0.5px;
        }

        /* Quebra de página */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    @foreach ($groupedExams as $index => $group)
    <section class="{{ $index < count($groupedExams) - 1 ? 'page-break' : '' }}">
        <header>
            <div class="logo-text">
                Clinica Exemplo
            </div>
            <div class="info-container">
                <div><strong>Realizado por:</strong> {{ $requesterName }}</div>
                <div><strong>Paciente:</strong> {{ $patientName }}</div>
                <div><strong>CPF:</strong> {{ $patientCpf }}</div>
                <div><strong>Telefone:</strong> {{ $patientPhone }}</div>
                <div><strong>Data de nascimento:</strong> {{ $patientBirthDate }}</div>
                <div><strong>Gênero:</strong> {{ $patientGender }}</div>
            </div>
        </header>

        @foreach ($group['groups'] as $subgroup)
        <table>
            <thead>
                <tr>
                    <th>Exame</th>
                    <th>Lateralidade</th>
                    <th>Comentário</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($subgroup['exams'] as $exam)
                <tr>
                    <td>{{ $exam['name'] }}</td>
                    <td class="laterality">{{ $exam['laterality'] ?? '-' }}</td>
                    <td class="comment">{{ $exam['comment'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if (!empty($subgroup['observation']))
        <p class="observation"><strong>Observação:</strong> {{ $subgroup['observation'] }}</p>
        @endif
        @endforeach

        <footer>
            {{ $location }},
            {{ now()->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y') }}<br />
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-name">{{ $doctorName }}</div>
            </div>
            Rua Fictícia, 123 - Bairro Exemplo - Cidade Fictícia - Estado - CEP 00000-000
        </footer>
    </section>
    @endforeach

</body>

</html>