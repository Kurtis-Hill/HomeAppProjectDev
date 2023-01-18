import * as React from 'react';

export function DisplayCardRequestContainer(props: { children?: React.ReactNode; classes?:string; cardViewID: number }): React {
    const classes = props.classes ?? 'col-xl-3 col-md-6 mb-4 hover';
    const cardViewID = props.cardViewID;

    const handleGeneralCardClick = () => {
        console.log('cardViewID', cardViewID);
    }
    return (
        <>
            <div className={classes} onClick={() => { handleGeneralCardClick() }}>
                { props.children }
            </div>
        </>
    );
}