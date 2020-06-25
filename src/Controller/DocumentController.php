<?php

namespace App\Controller;

use DocuSign\eSign\Api\EnvelopesApi;
use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Configuration;
use DocuSign\eSign\Model\Document;
use DocuSign\eSign\Model\EnvelopeDefinition;
use DocuSign\eSign\Model\Recipients;
use DocuSign\eSign\Model\RecipientViewRequest;
use DocuSign\eSign\Model\Signer;
use DocuSign\eSign\Model\SignHere;
use DocuSign\eSign\Model\Tabs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use DocuSign\eSign\Model;
class DocumentController extends AbstractController
{


    /**
     * @Route("/document", name="document")
     */
    public function index()
    {


        return $this->render('document/index.html.twig', [
            'controller_name' => 'DocumentController',
            'url'=> $this->embedded_signing_ceremony()
        ]);
    }


    public function embedded_signing_ceremony(){
        #
        # The document $fileNamePath will be signed by <signer_name> via an
        # embedded signing ceremony.

        # Settings
        # Fill in these constants
        #
        # Obtain an OAuth access token from https://developers.docusign.com/oauth-token-generator
        $accessToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImtpZCI6IjY4MTg1ZmYxLTRlNTEtNGNlOS1hZjFjLTY4OTgxMjIwMzMxNyJ9.eyJUb2tlblR5cGUiOjUsIklzc3VlSW5zdGFudCI6MTU4ODM0NzczNSwiZXhwIjoxNTg4Mzc2NTM1LCJVc2VySWQiOiJhZGVkZTJiZS1iYTE3LTQzZjctYTY4OC1jYjE5MzFiMDRjNGYiLCJzaXRlaWQiOjEsInNjcCI6WyJzaWduYXR1cmUiLCJjbGljay5tYW5hZ2UiLCJvcmdhbml6YXRpb25fcmVhZCIsInJvb21fZm9ybXMiLCJncm91cF9yZWFkIiwicGVybWlzc2lvbl9yZWFkIiwidXNlcl9yZWFkIiwidXNlcl93cml0ZSIsImFjY291bnRfcmVhZCIsImRvbWFpbl9yZWFkIiwiaWRlbnRpdHlfcHJvdmlkZXJfcmVhZCIsImR0ci5yb29tcy5yZWFkIiwiZHRyLnJvb21zLndyaXRlIiwiZHRyLmRvY3VtZW50cy5yZWFkIiwiZHRyLmRvY3VtZW50cy53cml0ZSIsImR0ci5wcm9maWxlLnJlYWQiLCJkdHIucHJvZmlsZS53cml0ZSIsImR0ci5jb21wYW55LnJlYWQiLCJkdHIuY29tcGFueS53cml0ZSJdLCJhdWQiOiJmMGYyN2YwZS04NTdkLTRhNzEtYTRkYS0zMmNlY2FlM2E5NzgiLCJhenAiOiJmMGYyN2YwZS04NTdkLTRhNzEtYTRkYS0zMmNlY2FlM2E5NzgiLCJpc3MiOiJodHRwczovL2FjY291bnQtZC5kb2N1c2lnbi5jb20vIiwic3ViIjoiYWRlZGUyYmUtYmExNy00M2Y3LWE2ODgtY2IxOTMxYjA0YzRmIiwiYW1yIjpbImludGVyYWN0aXZlIl0sImF1dGhfdGltZSI6MTU4ODM0NzczNCwicHdpZCI6ImU5MjI5NjdjLWVmYTQtNGQ4Mi1hZTA3LTBmYjY1MjY3OTFhMyJ9.QV74OOfolVrFEXgEvyiaprkfvWS56FZtG_2LyWVjqNm9Ztv1Bss4ToJxRhWGST29Emq2mEJhZid45qoq15xFTFeUQdmd_Et9Sq30PXqV0M_WkFK03EkoVR_nrItokankK8UKCzArvQrgoBBnuuj9Vsdh-2agIXxJRvaTd12jQB_8jozO2BJSEHPc-1i5fnoJx2Iwz6nNWenhrjzSLTIBDpfaPgZ582SuZN3yshq-wMgcXLBoaeg76DTVlxRy_Ony3XYbmFvJeIfBrJhY8R14GoR6EgQkm6D6kOxVcNERl3qJg7yRCIDkOdtoV7Q8Eer6XqWCJAn5SBKmqlTjJV27TQ';
        # Obtain your accountId from demo.docusign.com -- the account id is shown in the drop down on the
        # upper right corner of the screen by your picture or the default picture.
        $accountId = '10429553';
        # Recipient Information:
        $signerName = 'Arturo Guerra';
        $signerEmail = 'guerraarturo@icloud.com';
        # The document you wish to send. Path is relative to the root directory of this repo.
        $fileNamePath = 'Documents/World_Wide_Corp_lorem.pdf';
        # The url of this web application's folder. If you leave it blank, the script will attempt to figure it out.
        $baseUrl = '';
        $clientUserId = '123'; # Used to indicate that the signer will use an embedded
        # Signing Ceremony. Represents the signer's userId within
        # your application.
        $authenticationMethod = 'None'; # How is this application authenticating
        # the signer? See the `authenticationMethod' definition
        # https://developers.docusign.com/esign-rest-api/reference/Envelopes/EnvelopeViews/createRecipient

        # The API base_path
        $basePath = 'https://demo.docusign.net/restapi';

        # Constants
        $appPath = getcwd();

        #
        # Step 1. The envelope definition is created.
        #         One signHere tab is added.
        #         The document path supplied is relative to the working directory
        #
        # Create the component objects for the envelope definition...
        $contentBytes = file_get_contents($appPath . "/" . $fileNamePath);
        $base64FileContent =  base64_encode ($contentBytes);

        # create the DocuSign document object
        $document = new Document([
            'document_base64' => $base64FileContent,
            'name' => 'Example document', # can be different from actual file name
            'file_extension' => 'pdf', # many different document types are accepted
            'document_id' => '1' # a label used to reference the doc
        ]);

        # The signer object
        $signer = new Signer([
            'email' => $signerEmail, 'name' => $signerName, 'recipient_id' => "1", 'routing_order' => "1",
            'client_user_id' => $clientUserId # Setting the client_user_id marks the signer as embedded
        ]);

        # DocuSign SignHere field/tab object
        $signHere = new SignHere([
            'document_id' => '1', 'page_number' => '1', 'recipient_id' => '1',
            'tab_label' => 'SignHereTab', 'x_position' => '195', 'y_position' => '147'
        ]);

        # Add the tabs to the signer object
        # The Tabs object wants arrays of the different field/tab types
        $signer->setTabs(new Tabs(['sign_here_tabs' => [$signHere]]));

        # Next, create the top level envelope definition and populate it.
        $envelopeDefinition = new EnvelopeDefinition([
            'email_subject' => "Please sign this document",
            'documents' => [$document], # The order in the docs array determines the order in the envelope
            # The Recipients object wants arrays for each recipient type
            'recipients' => new Recipients(['signers' => [$signer]]),
            'status' => "sent" # requests that the envelope be created and sent.
        ]);

        #
        #  Step 2. Create/send the envelope.
        #
        $config = new Configuration();
        $config->setHost($basePath);
        $config->addDefaultHeader("Authorization", "Bearer " . $accessToken);
        $apiClient = new ApiClient($config);
        $envelopeApi = new EnvelopesApi($apiClient);
        $results = $envelopeApi->createEnvelope($accountId, $envelopeDefinition);
        $envelopeId = $results['envelope_id'];

        #
        # Step 3. The envelope has been created.
        #         Request a Recipient View URL (the Signing Ceremony URL)
        #
        if ($baseUrl == '') {
            # Try to figure out our URL folder
            # NOTE: The following code relies on browser-supplied headers to be correct.
            #       In production, DO NOT use this code since it is not bullet-proof.
            #       Instead, set the $baseUrl appropriately.
            $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            # remove the current script name from the $baseUrl:
            $chars = -1 * (strlen(__FILE__) - strlen(__DIR__));
            $baseUrl = substr($baseUrl, 0, $chars);
        }
        $recipientViewRequest = new RecipientViewRequest([
            'authentication_method' => $authenticationMethod, 'client_user_id' => $clientUserId,
            'recipient_id' => '1', 'return_url' => $baseUrl . '/dsreturn.php',
            'user_name' => $signerName, 'email' => $signerEmail
        ]);

        $results = $envelopeApi->createRecipientView($accountId, $envelopeId,
            $recipientViewRequest);

        #
        # Step 4. The Recipient View URL (the Signing Ceremony URL) has been received.
        #         The user's browser will be redirected to it.
        #
        return $results['url'];
    }
}
