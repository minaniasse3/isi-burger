<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture - ISI BURGER</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #e74c3c;
            margin-bottom: 5px;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-info div {
            width: 45%;
        }
        .invoice-details {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
        }
        .text-right {
            text-align: right;
        }
        .total {
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ISI BURGER</h1>
            <p>Facture #{{ $order->id }}</p>
        </div>
        
        <div class="invoice-info">
            <div>
                <h3>Facturé à:</h3>
                <p>
                    {{ $order->user->name }}<br>
                    {{ $order->user->email }}<br>
                    {{ $order->user->address ?? 'Adresse non spécifiée' }}<br>
                    {{ $order->user->phone ?? 'Téléphone non spécifié' }}
                </p>
            </div>
            <div>
                <h3>Informations:</h3>
                <p>
                    Date: {{ $order->created_at->format('d/m/Y') }}<br>
                    Statut: {{ $order->status_label }}<br>
                    Méthode de paiement: {{ $order->payment_method === 'especes' ? 'Espèces' : 'Carte bancaire' }}
                </p>
            </div>
        </div>
        
        <div class="invoice-details">
            <h3>Détails de la commande</h3>
            <table>
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix unitaire</th>
                        <th>Quantité</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->burger->name }}</td>
                        <td>{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right total">Total</td>
                        <td class="text-right total">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="footer">
            <p>Merci d'avoir choisi ISI BURGER. Nous espérons vous revoir bientôt!</p>
            <p>Pour toute question, contactez-nous à contact@isiburger.com</p>
        </div>
    </div>
</body>
</html> 