<?php
declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use App\Application\UseCase\Wallet\UpdateExchangeRate;
use App\Application\UseCase\Wallet\AdminAdjustBalance;
use App\Application\UseCase\Wallet\GetActiveExchangeRate;
use App\Domain\Wallet\RechargePackageRepositoryInterface;

class AdminWalletController {
    public function __construct(
        private Twig $twig,
        private UpdateExchangeRate $updateExchangeRate,
        private AdminAdjustBalance $adminAdjustBalance,
        private GetActiveExchangeRate $getActiveExchangeRate,
        private RechargePackageRepositoryInterface $packageRepo
    ) {}

    public function index(Request $request, Response $response): Response {
        $rate = $this->getActiveExchangeRate->execute();
        $packages = $this->packageRepo->findAllActive();
        
        return $this->twig->render($response, 'pages/admin/wallet.twig', [
            'title' => 'Admin Monedero ZEN',
            'rate' => $rate,
            'packages' => $packages
        ]);
    }

    public function updateRate(Request $request, Response $response): Response {
        $data = (array)$request->getParsedBody();
        $currency = $data['currency'] ?? 'PEN';
        $rate = (float)($data['rate'] ?? 3.5);
        
        $userId = '';
        $rawCookie = $_COOKIE['kaijuu_session'] ?? '';
        if ($rawCookie) {
            $sess = json_decode(urldecode($rawCookie), true) ?: json_decode($rawCookie, true);
            if (!empty($sess['id'])) {
                $userId = (string)$sess['id'];
            }
        }
        
        $this->updateExchangeRate->execute($currency, $rate, $userId);
        return $response->withHeader('Location', '/admin/monedero')->withStatus(302);
    }

    public function adjustBalance(Request $request, Response $response): Response {
        $data = (array)$request->getParsedBody();
        $userId = $data['user_id'];
        $amount = (float)$data['amount_zen'];
        $direction = $data['direction'];
        $notes = $data['notes'] ?? '';

        $this->adminAdjustBalance->execute($userId, $amount, $direction, $notes);
        return $response->withHeader('Location', '/admin/monedero')->withStatus(302);
    }
}
