<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitação de Exames</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Arimo:ital,wght@0,400..700;1,400..700&display=swap"
        rel="stylesheet">
    <style>
        @page {
            margin: 2.5cm 1cm 3cm 1cm;
        }

        header {
            position: fixed;
            top: -60px;
            left: 0px;
            right: 0px;
            height: 50px;

            text-align: center;
            line-height: 20px;
        }

        body {
            margin-top: 0;
            padding: 0;
            font-family: "Arimo", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-size: 14px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 50px;

            text-align: center;

            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .fs-small {
            font-size: 10px;
        }

        .fw-bold {
            font-weight: bold;
        }

        table {
            border: 0;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
        }

        .table-simple {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .table-simple td {
            padding: 0.5em;
        }

        .table-simple td,
        .table-simple th {
            border: 1px solid black;
        }

        .mb-1 {
            margin-bottom: 1em;
        }

        .mb-2 {
            margin-bottom: 2em;
        }

        .mb-3 {
            margin-bottom: 3em;
        }

        .mb-4 {
            margin-bottom: 4em;
        }

        .mb-5 {
            margin-bottom: 5em;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <header>
        <table width="100%">
            <tr>
                <td>
                    <img src="assets/img/logo-full.png" style="width: 153px; height: 30px;" />
                </td>
            </tr>
            <tr>
                <td>
                    <h2 style="margin: 0;padding: 0; text-align: center;">
                        Solicitação de Exames
                    </h2>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        São Paulo, {{ \Carbon\Carbon::now()->translatedFormat('d \d\e F \d\e Y') }}
        <br><br>
        ________________________________
        <br>
        Dr. <?= $doctor['full_name'] ?>
        <br>
        CRM <?= $doctor['crm'] ?>
        <br>
        Praça da Sé, 1, Sé, CEP 01001-000, São Paulo - SP Telefones (11) 1234-5678 e (11) 9999-0000
    </footer>

    @foreach ($groupedExams as $page => $group)
        <p class="mb-2">
            <span class="fw-bold">Realizado por:</span>
            Dr. <?= $doctor['full_name'] ?>
            <br>
            <span class="fw-bold">Paciente:</span>
            <?= $patient['full_name'] ?>
            <br>
            <span class="fw-bold">CPF:</span>
            <?= $patient['document'] ?>
        </p>

        @foreach ($group['packages'] as $package)
            <table class="table-simple mb-2">
                <thead>
                    <tr class="fw-bold">
                        <td width="80%">
                            Exame
                        </td>
                        <td>
                            Lat.
                        </td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($package['exams'] as $exam)
                        <tr>
                            <td>
                                <?= $exam['name'] ?>
                                @if ($exam['comment'])
                                    <br>
                                    <span class="fs-small"><?= $exam['comment'] ?></span>
                                @endif
                            </td>
                            <td>
                                <?= $exam['laterality'] ?? '-' ?>
                            </td>
                        </tr>
                    @endforeach
                    @if (!empty($package['package_observations']))
                        <tr>
                            <td colspan="2">
                                Observação: <?= $package['package_observations'] ?>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @endforeach
        @if ($page + 1 < count($groupedExams))
            <div class="page-break"></div>
        @endif
    @endforeach
</body>

</html>
