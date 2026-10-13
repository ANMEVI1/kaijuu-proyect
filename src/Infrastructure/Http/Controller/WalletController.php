<?php
declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use App\Application\UseCase\Wallet\GetWalletBalance;
use App\Application\UseCase\Wallet\GetRechargePackages;
use App\Application\UseCase\Wallet\GetActiveExchangeRate;
use App\Application\UseCase\Wallet\InitiateRecharge;
use App\Application\UseCase\Wallet\ConfirmRecharge;
use App\Domain\Wallet\WalletRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;

class WalletController {
    public function __construct(
        private Twig $twig,
        private GetWalletBalance $getWalletBalance,
        private GetRechargePackages $getRechargePackages,
        private GetActiveExchangeRate $getActiveExchangeRate,
        private InitiateRecharge $initiateRecharge,
        private ConfirmRecharge $confirmRecharge,
        private WalletRepositoryInterface $walletRepo,
        private UserRepositoryInterface $userRepo
    ) {}

    private function getUserId(?Request $request = null): ?string {
        if ($request && $request->hasHeader('X-User-Id')) {
            $header = $request->getHeaderLine('X-User-Id');
            if ($header) return $header;
        }
        if ($request) {
            $params = $request->getQueryParams();
            if (!empty($params['user_id'])) return (string)$params['user_id'];
            $body = (array)$request->getParsedBody();
            if (!empty($body['user_id'])) return (string)$body['user_id'];
        }

        $rawCookie = $_COOKIE['kaijuu_session'] ?? '';
        if ($rawCookie) {
            $session = json_decode(urldecode($rawCookie), true);
            if (!is_array($session)) {
                $session = json_decode($rawCookie, true);
            }
            if (is_array($session)) {
                if (!empty($session['id'])) {
                    return (string)$session['id'];
                }
                if (!empty($session['email'])) {
                    $user = $this->userRepo->findByEmail((string)$session['email']);
                    if ($user) return $user->id;
                }
            }
        }
        return null;
    }

    public function index(Request $request, Response $response): Response {
        $userId = $this->getUserId($request);
        if (!$userId) return $response->withHeader('Location', '/')->withStatus(302);

        $wallet = $this->getWalletBalance->execute($userId);
        $rate = $this->getActiveExchangeRate->execute();
        $transactions = $this->walletRepo->getTransactions($wallet->id, 5);

        return $this->twig->render($response, 'pages/wallet/index.twig', [
            'title' => 'Mi Monedero ZEN',
            'wallet' => $wallet,
            'rate' => $rate,
            'transactions' => $transactions
        ]);
    }

    public function rechargeForm(Request $request, Response $response): Response {
        $userId = $this->getUserId($request);
        if (!$userId) return $response->withHeader('Location', '/')->withStatus(302);

        $packages = $this->getRechargePackages->execute();
        $rate = $this->getActiveExchangeRate->execute();

        return $this->twig->render($response, 'pages/wallet/recharge.twig', [
            'title' => 'Recargar ZEN',
            'packages' => $packages,
            'rate' => $rate
        ]);
    }

    public function initiateRecharge(Request $request, Response $response): Response {
        $userId = $this->getUserId($request);
        if (!$userId) return $response->withHeader('Location', '/')->withStatus(302);

        $data = (array)$request->getParsedBody();
        $packageId = $data['package_id'] ?? '';
        $paymentMethod = $data['payment_method'] ?? 'paypal';

        try {
            $result = $this->initiateRecharge->execute($userId, $packageId, $paymentMethod);
            
            // Modo simulación: acreditar ZEN inmediatamente sin gateway real
            $this->confirmRecharge->execute($result['reference_code'], 'completado');

            return $response->withHeader('Location', '/monedero/recargar/exitoso?reference_code=' . $result['reference_code'])->withStatus(302);
        } catch (\Exception $e) {
            return $response->withHeader('Location', '/monedero/recargar?error=1')->withStatus(302);
        }
    }

    public function rechargeSuccess(Request $request, Response $response): Response {
        return $this->twig->render($response, 'pages/wallet/success.twig', [
            'title' => 'Recarga Exitosa'
        ]);
    }

    public function history(Request $request, Response $response): Response {
        $userId = $this->getUserId($request);
        if (!$userId) return $response->withHeader('Location', '/')->withStatus(302);

        $page = max(1, (int)($request->getQueryParams()['page'] ?? 1));
        $limit = 20;

        $wallet = $this->getWalletBalance->execute($userId);
        $transactions = $this->walletRepo->getTransactions($wallet->id, $limit);

        return $this->twig->render($response, 'pages/wallet/history.twig', [
            'title' => 'Historial ZEN',
            'wallet' => $wallet,
            'transactions' => $transactions,
            'currentPage' => $page,
            'totalPages' => 1
        ]);
    }

    // ── JSON API ENDPOINTS ───────────────────────────────────────────────────

    public function apiGetBalance(Request $request, Response $response): Response {
        $userId = $this->getUserId($request);
        if (!$userId) {
            $response->getBody()->write(json_encode(['error' => 'No autenticado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $wallet = $this->getWalletBalance->execute($userId);
        $rate = $this->getActiveExchangeRate->execute();

        $data = [
            'wallet_id' => $wallet->id,
            'user_id' => $wallet->userId,
            'balance_zen' => $wallet->balanceZen,
            'rate_pen' => $rate ? $rate->rate : 3.50,
            'balance_pen' => $rate ? ($wallet->balanceZen * $rate->rate) : ($wallet->balanceZen * 3.50)
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function apiGetPackages(Request $request, Response $response): Response {
        $packages = $this->getRechargePackages->execute();
        $response->getBody()->write(json_encode($packages));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function apiGetExchangeRate(Request $request, Response $response): Response {
        $rate = $this->getActiveExchangeRate->execute();
        $response->getBody()->write(json_encode($rate));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
