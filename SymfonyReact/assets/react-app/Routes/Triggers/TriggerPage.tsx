import * as React from 'react';
import { useState, useEffect } from 'react';
import { SensorTriggerResponseInterface } from '../../Sensors/Response/Sensor/Trigger/SensorTriggerResponseInterface';
import { getAllSensorTriggerTypesRequest } from '../../Sensors/Request/Sensor/Trigger/GetAllTriggersRequest';
import BaseModal from '../../Common/Components/Modals/BaseModal';
import SubmitButton from '../../Common/Components/Buttons/SubmitButton';
import CloseButton from '../../Common/Components/Buttons/CloseButton';
import TriggerCard from '../../Sensors/Components/Trigger/TriggerCard';
import DotCircleSpinner from '../../Common/Components/Spinners/DotCircleSpinner';

export default function TriggerPage() {
    const [triggerData, setTriggerData] = useState<SensorTriggerResponseInterface[]>([]);

    const [loadingTriggerData, setLoadingTriggerData] = useState<boolean>(true);

    const [triggerDataErrors, setTriggerDataErrors] = useState<Array<string>>([]);

    const [addNewModal, setAddNewModal] = useState<boolean>(false);

    const [showDeleteModal, setShowDeleteModal] = useState<boolean>(false);

    const [selectedTriggerID, setSelectedTriggerID] = useState<number|null>(null);

    const fetchAllTriggerData = async () => {
        setLoadingTriggerData(true);
        const response = await getAllSensorTriggerTypesRequest();
        console.log(response.data.payload);
        if (response.status === 200 && Array.isArray(response.data.payload)) {
            setTriggerData(response.data.payload);
            setLoadingTriggerData(false);
        } else {
            setTriggerDataErrors(response.data.errors);
            setLoadingTriggerData(false);
        }
    }

    const handleShowDeleteModal = (triggerID: number|null) => {
        if (triggerID === null) {
            setShowDeleteModal(false);    
        } else {
            setSelectedTriggerID(triggerID);
            setShowDeleteModal(true);
        }
    }

    const deleteTrigger = async (e: Event) => {
        // const response = await deleteTriggerRequest(triggerID);
        // if (response.status === 200) {
        //     fetchAllTriggerData();
        // }
    } 

    useEffect(() => {
        fetchAllTriggerData();
    }, []);

    return (
        <>  
            <div id="content-wrapper" className="d-flex flex-column">
                <div id="content"> 
                    <div className="container-fluid">
                        {/* <div className="row"> */}
                            <h1>Triggers</h1>
                            {
                                loadingTriggerData === true
                                    ?
                                        <DotCircleSpinner spinnerSize={5} classes="center-spinner-card-row hidden-scroll" />
                                    :                                        
                                        triggerData.length > 0
                                            ? 
                                                triggerData.map((sensorTriggerData: SensorTriggerResponseInterface, index: number) => {
                                                    return (
                                                        <>
                                                            <div key={index}>
                                                                <TriggerCard 
                                                                    sensorTriggerData={sensorTriggerData} 
                                                                    handleShowDeleteModal={handleShowDeleteModal}
                                                                />
                                                            </div>
                                                        </>
                                                    )
                                                })
                                            : 
                                                <h2>No Triggers to dispay</h2>

                            }
                            {
                                showDeleteModal === true
                                    ?
                                        <BaseModal
                                            title={`Delete Trigger`}
                                            modalShow={showDeleteModal}
                                            setShowModal={setShowDeleteModal}
                                        >
                                            <>
                                                Delete trigger ID: <b>{selectedTriggerID}</b>?
                                                <br />
                                                <SubmitButton
                                                    type="submit"
                                                    text='Delete Device'
                                                    name='delete-device'
                                                    action='submit'
                                                    classes='add-new-submit-button'
                                                    onClickFunction={deleteTrigger}
                                                />
                                                <CloseButton 
                                                    close={setShowDeleteModal} 
                                                    classes={"modal-cancel-button"} 
                                                />
                                            </>                                                   
                                        </BaseModal>
                                    :
                                        null
                            }
                    </div>
                </div>
            </div>
        </>
    );
}