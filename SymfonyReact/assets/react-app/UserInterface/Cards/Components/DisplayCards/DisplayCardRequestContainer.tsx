import * as React from 'react';

export function DisplayCardRequestContainer(props: { 
    children?: React.ReactNode; 
    classes?:string; 
    cardViewID: number; 
    setSelectedCardForQuickUpdate: (cardViewID: number) => void; 
}): React {
    const classes = props.classes ?? 'col-xl-3 col-md-6 mb-4 hover';
    const cardViewID = props.cardViewID;

    const handleGeneralCardClick = () => {
        props.setSelectedCardForQuickUpdate(cardViewID);
    }
    
    return (
        <React.Fragment>
            <div className={classes} onClick={() => { handleGeneralCardClick() }}>
                { props.children }
            </div>
        </React.Fragment>
    );
}