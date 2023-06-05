import * as React from 'react';
import { useState } from 'react';
import { AddNewSensor } from './AddNewSensor';
import BaseModal from '../../../Common/Components/Modals/BaseModal';

export function AddNewSensorButton(props: {deviceID: number}) {
    const { deviceID } = props;

    const [showModal, setShowModal] = useState<boolean>(false);
    
    const showAddNewSensorModal = () => {
        setShowModal(true);
    }

    return (
        <>
            <button className="btn btn-primary">+Add New Sensor</button>
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
                                <AddNewSensor deviceID={deviceID} />
                            </BaseModal>
                        </>
                    :
                        null
            }
        </>
    )
}