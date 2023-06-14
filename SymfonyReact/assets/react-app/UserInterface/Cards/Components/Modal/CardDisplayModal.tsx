import * as React from 'react';
import { useState, useEffect } from 'react';
import BaseModal from '../../../../Common/Components/Modals/BaseModal';
import { TabSelector } from '../../../../Common/Components/TabSelector';
import { UpdateCard } from '../Form/UpdateCard';

const cardViewUpdate: string = 'Card View Update';
const boundaryUpdate: string = 'Boundary Update';

export function CardDisplayModal(props: {
    cardViewID: number|null;     
    loadingCardModalView: boolean;
    setLoadingCardModalView: (loadingCardModalView: boolean) => void;
}) {
    const { cardViewID, loadingCardModalView, setLoadingCardModalView } = props;
    const [loading, setLoading] = useState<boolean>(false);
    const [tabSelection, setTabSelection] = useState<string[]>('Card View Update');
    
    const tabOptions = [
        cardViewUpdate,
        boundaryUpdate,
    ];

    useEffect(() => {
        if (tabSelection === 'Boundary Update') {
            // const sensorResponse = sensorRequest
            //do sensor request by cardview ID

        }
    }, [tabSelection])
    // type CardModalDisplayTabOptions = {
    //     cardViewUpdate: string,
    //     boundaryUpdate: string,
    // }

    // const cardModalDisplayTabOptions: CardModalDisplayTabOptions = {
    //     cardViewUpdate: 'Card View Update',
    //     boundaryUpdate: 'Boundary Update',
    // }

    // have different componenets for base modal children,
    // boundary update form,
    // card view update form,

    console.log('cardVieID me too', cardViewID, setLoadingCardModalView, loadingCardModalView)
    return (
        <>
            <BaseModal
                title={'Update'}
                modalShow={loadingCardModalView}
                setShowModal={setLoadingCardModalView}
            >
                <div className="container" style={{ textAlign: "center", margin: "inherit"}}>
                    <TabSelector
                        currentTab={tabSelection}
                        setCurrentTab={setTabSelection}
                        options={tabOptions}
                    />

                    {
                        tabSelection === tabOptions[0]
                            ? <UpdateCard
                                cardViewID={cardViewID}
                                />
                            : null
                    }            

                    {
                        tabSelection === tabOptions[1]

                    }    
                </div>
            </BaseModal>
        </>
    )
}