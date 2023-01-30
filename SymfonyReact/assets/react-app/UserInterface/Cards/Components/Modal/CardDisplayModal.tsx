import * as React from 'react';
import { useState } from 'react';
import BaseModal from '../../../../Common/Components/Modals/BaseModal';

import { CardDisplayForm } from "../Form/CardDisplayForm";
import { CardFormResponseInterface } from '../Response/FormResponse/CardFormResponseInterface';

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
    // const [cardFormData, setCardFormData] = useState<CardFormResponseInterface>();
    // const [cardViewID, setCardViewID] = useState<number|null>(null);
    const [tabSelection, setTabSelection] = useState<CardDisplayModalTabs>('cardViewUpdate');
    


    // have different componenets for base modal children,
    // boundary update form,
    // card view update form,

    console.log('cardVieID', cardViewID)
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