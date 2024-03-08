

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\QrCode;

class QrCodeController extends AbstractController
{
    /**
     * @Route("/qrcode/{data}", name="generate_qr_code")
     */
    public function generateQrCode($data)
    {
        $qrCode = new QrCode($data);

        // Set additional options if needed
        // $qrCode->setSize(300);

        // Return response with image
        return new Response($qrCode->writeString(), 200, [
            'Content-Type' => $qrCode->getContentType()
        ]);
    }
}
