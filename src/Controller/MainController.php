<?php

namespace App\Controller;

use App\Service\Application\LogAnalyzerRequest;
use App\Service\Application\LogAnalyzerService;
use Exception;
use Laminas\View\Model\JsonModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutowireTrait;
use Symfony\Flex\Response;

class MainController extends AbstractController
{
    use AutowireTrait; // Autowiring in Laminas

    private const OUTDATED_PERIOD = '-10 days';
    private string $logsDir = 'data/logs/test-1.logs';

    /**
     * @dataProvider Provider
     * @return Response
     */
    public function index(
        LogAnalyzerService $service,
        ?string $outdatedTimeParam = null,
        ?string $logsDir = null
    ): JsonModel {
        $outdatedTime = $outdatedTimeParam ?? self::OUTDATED_PERIOD; //can be used $this->getParameter();
        $this->logsDir = $logsDir ?? $this->logsDir; //can be used $this->getParameter();

        $serviceRequest = new LogAnalyzerRequest($this->logsDir, $outdatedTime);
        $result = ['status' => true, 'messages' => ''];

        try {
            $result['status'] = $service->execute($serviceRequest);
        } catch (Exception $exception) {
            $result = [
                'status' => false,
                'messages' => $exception->getMessage(),
            ];
        }

        var_dump($result);
        return new JsonModel($result); // JsonResponse for Symfony
    }
}
