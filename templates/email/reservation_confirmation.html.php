<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Confirmation de réservation</title>
</head>
<body>
    <h1>Bonjour {{ user.username }}</h1>

    <p>Votre réservation chez <strong>{{ reservation.commerce.name }}</strong> est confirmée !</p>

    <p><strong>Détails de votre séjour :</strong></p>
    <ul>
        <li>Arrivée : {{ reservation.dateArrivee|date('d/m/Y') }}</li>
        <li>Départ : {{ reservation.dateDepart|date('d/m/Y') }}</li>
        <li>Nombre d’adultes : {{ reservation.nombreAdultes }}</li>
        <li>Nombre d’enfants : {{ reservation.nombreEnfants }}</li>
        <li>Nombre de chambres : {{ reservation.nombreChambres }}</li>
        <li>Total : {{ reservation.total }} €</li>
    </ul>

    <p>Merci pour votre confiance et à bientôt !</p>
</body>
</html>
