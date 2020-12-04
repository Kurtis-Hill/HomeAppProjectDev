<?php


namespace App\Services;


use App\Entity\Core\Sensornames;
use App\HomeAppCore\HomeAppRoomAbstract;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\FormInterface;

class SensorService extends HomeAppRoomAbstract
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * @param array $sensorData
     * @param FormInterface $addNewSensorForm
     * @return FormInterface
     */
    public function handleNewSensorFormSubmission(array $sensorData, FormInterface $addNewSensorForm): FormInterface
    {
        $this->userInputDataCheck($sensorData);

        return $this->processNewSensorForm($addNewSensorForm, $sensorData);
    }


    /**
     * @param FormInterface $addNewSensorForm
     * @param array $sensorData
     * @return FormInterface
     */
    private function processNewSensorForm(FormInterface $addNewSensorForm, array $sensorData): FormInterface
    {
        $addNewSensorForm->submit($sensorData);

        if ($addNewSensorForm->isSubmitted() && $addNewSensorForm->isValid()) {
            try {
                $this->em->persist($addNewSensorForm->getData());
                $this->em->flush();
            } catch (ORMException $e) {
                error_log($e->getMessage());
            } catch (\PDOException $e) {
                $errorMessage['errors'] = $e->getMessage();
            } catch (\Exception $e) {
                error_log($e->getMessage());
            }
        } else {
            foreach ($addNewSensorForm->getErrors(true, true) as $error) {
                array_push($this->errors, $error->getMessage());
            }
        }

        return $addNewSensorForm;
    }


    /**
     * @param array $sensorData
     * //@TODO needs query adjustment to finy by one for specific group
     */
    private function userInputDataCheck(array $sensorData)
    {
        $currentUserSensorNameCheck = $this->em->getRepository(Sensornames::class)->findOneBy(['sensorname' => $sensorData['sensorname']]);

        if (!empty($currentUserSensorNameCheck)) {
            $this->errors[] = 'You already have a sensor named '. $sensorData['sensorname'];
        }

        if (!in_array($sensorData['groupnameid'], $this->getGroupNameID())) {
            $this->errors[] = 'You are not part of this group';
        }
    }

    public function handleSensorDataUpdate()
    {

    }

    private function processSensorDataUpdate()
    {

    }


    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

}