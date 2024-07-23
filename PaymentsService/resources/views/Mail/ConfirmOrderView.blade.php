<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @php
    $details = $order['details'];
    @endphp
    @php
    $cartItems = $order['cart']['cartItem'];
    @endphp
</head>

<body>
    <div class="flex flex-col items-center gap-5 m-8">
        <div class="flex gap-2.5 items-center">
            <i class="bi bi-basket3-fill text-2xl"></i>
            <hr class="w-px h-full bg-black" />
            <p class="font-extrabold text-lg tracking-wider">E-commerce</p>
        </div>
        <div class="w-full flex flex-col gap-3.5">
            <span class="font-bold tracking-wider">
                Order: <span class="text-[#0B6BDA] tracking-wider">#{{$order['id']}}</span>
                <br />
                Created at: {{$order['createdAt']}}
            </span>
            <span class="tracking-wider">
                Hello <span class="font-bold tracking-wider">{{ $order['details']['firstName'] }} {{ $order['details']['lastName'] }}</span>,
                <br />
                Order: #{{$order['id']}} was <span class="font-bold tracking-wider">{{$order['payment']['status']['name']}}</span>
                <br />
                <span class="font-bold tracking-wider">Reason:</span> {{$order['payment']['notes']}}
            </span>
        </div>
        <table class="w-full">
            <thead>
                <tr>
                    <th data-priority="1" align="left"></th>
                    <th data-priority="2" align="left">Products</th>
                    <th data-priority="3">Quantity</th>
                    <th data-priority="4" align="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cartItems as $item)
                <tr>
                    <td align="center">
                        <img class="w-20" src="{{$item['product']['image']}}" alt="{{$item['product']['title']}}" />
                    </td>
                    <td>{{ $item['product']['title'] }}</td>
                    <td align="center">{{ $item['quantity'] }} x</td>
                    <td align="right">{{ $item['productTotal'] }} €</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="w-full h-auto flex flex-col gap-2.5">
            <span class="text-lg font-bold tracking-wider">Order Details</span>
            <div class="flex flex-col gap-2.5">
                <span class="tracking-wider">{{ $order['details']['firstName'] }} {{ $order['details']['lastName'] }}</span>
                <span class="tracking-wider">{{ $order['details']['address'] }}</span>
                <span class="tracking-wider">{{ $order['details']['city'] }}</span>
                <span class="tracking-wider">{{ $order['details']['postalCode'] }}</span>
            </div>
        </div>
        <span class="font-bold tracking-wider">See you soon. E-commerce team</span>
    </div>
</body>

</html>