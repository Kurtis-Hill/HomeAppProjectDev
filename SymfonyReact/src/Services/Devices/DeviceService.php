<?php


namespace App\Services\Devices;


use App\Entity\Core\Devices;
use App\HomeAppCore\HomeAppRoomAbstract;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class DeviceService extends HomeAppRoomAbstract
{
    /**
     * @param Request $request
     * @param $addNewDeviceForm
     */
    public function handleNewDeviceSubmission(Request $request, $addNewDeviceForm)
    {
        $deviceName = $request->get('device-name');
        $groupName = $request->get('group-name');

        if (!in_array($groupName, $this->getGroupNameID())) {
            $errors[] = 'You are not part of this group';
        }

        $currentUserDevices = $this->em->getRepository(Devices::class)->returnAllUsersDevices($this->getGroupNameID());

        foreach ($currentUserDevices as $value) {
            if ($value['devicename'] === $deviceName) {
                $errors[] = 'Your group already has a device named'. $deviceName;
            }
        }

        if (!empty($errors)) {
            return $errors;
        }
        else {
            $processedForm = $this->proccessNewDeviceForm($addNewDeviceForm, $request->get('device-name'), $request);

            return $processedForm;
        }
    }

    private function proccessNewDeviceForm(FormInterface $addNewDeviceForm, $deviceName, Request $request)
    {
        $addNewDeviceForm->submit([
            'devicename' => $request->get('device-name'),
            'groupnameid' => $request->get('group-name'),
            'roomid' => $request->get('room-name'),
        ]);

        if ($addNewDeviceForm->isSubmitted() && $addNewDeviceForm->isValid()) {
            $deviceName .= time();
            $secret = hash("md5", $deviceName);

            $validFormData = $addNewDeviceForm->getData();
            $validFormData->setSecret($secret);

            try {
                $this->em->persist($validFormData);
                $this->em->flush();
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();

                return $errors;
            }

            return $secret;
        }
        else {
            return $addNewDeviceForm;
        }


    }
}