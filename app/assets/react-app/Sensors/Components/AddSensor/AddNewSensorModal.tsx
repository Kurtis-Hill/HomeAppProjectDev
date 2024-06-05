import * as React from 'react';
import { useState } from 'react';
import { AddNewSensorForm } from './AddNewSensorForm';
import BaseModal from '../../../Common/Components/Modals/BaseModal';

export function AddNewSensorModal(props: {deviceID: number, refreshData?: () => void;}) {
    const { deviceID, refreshData } = props;

    const [showModal, setShowModal] = useState<boolean>(false);
    
    const toggleAddNewSensorModal = () => {
        setShowModal((showModal: boolean) => !showModal);
    }

    return (
        <>
            <button onClick={() => toggleAddNewSensorModal()} className="btn btn-primary">+Add New Sensor</button>
            {
                showModal === true
                    ?
                        <>
                            <BaseModal
                                title={"Add New Sensor"}
                                modalShow={showModal}
                                setShowModal={setShowModal}
                                heightClasses="snap-modal-height"
                            >
                                <AddNewSensorForm deviceID={deviceID} setShowModal={setShowModal} refreshData={refreshData} />

                            </BaseModal>
                        </>
                    :
                        null
            }
        </>
    )
}
