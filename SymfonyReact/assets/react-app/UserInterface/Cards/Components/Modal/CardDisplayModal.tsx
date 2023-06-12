import * as React from 'react';
import { useState } from 'react';
import BaseModal from '../../../../Common/Components/Modals/BaseModal';


enum CardDisplayModalTabs {
    cardViewUpdate = 'cardViewUpdate',
    boundaryUpdate = 'boundaryUpdate',
}

export function CardDisplayModal(props: {
    cardViewID: number|null;     
    loadingCardModalView: boolean;
    setLoadingCardModalView: (loadingCardModalView: boolean) => void;
}) {
    const { cardViewID, loadingCardModalView, setLoadingCardModalView } = props;
    const [loading, setLoading] = useState<boolean>(false);
    const [tabSelection, setTabSelection] = useState<CardDisplayModalTabs>('cardViewUpdate');
    


    // have different componenets for base modal children,
    // boundary update form,
    // card view update form,

    console.log('cardVieID me too', cardViewID)
    return (
        <>
            <BaseModal
                title={'updateID'}
                modalShow={false}
                setShowModal={() => {}}
            >
                
            </BaseModal>
        </>
    )
}