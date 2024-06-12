import * as React from 'react';
import { CardReadingFactoryInterface } from '../../Factories/CardReadingFactory';
import { CardSensorDataResponseInterface } from '../../Response/Cards/CurrentReadingCardData/CardDataResponseInterface';
import { StandardCurrentSensorReadingsCardView } from '../../Components/DisplayCards/StandardCurrentSensorReadingsCardView';

export default function CardCurrentReadingBuilder(
    props: CardReadingFactoryInterface
): React.ReactElement {
    const cardData: CardSensorDataResponseInterface = props.cardData;

    return (
        <StandardCurrentSensorReadingsCardView
            cardViewID={cardData.cardViewID}
            sensorType={cardData.sensorType}
            sensorName={cardData.sensorName}
            room={cardData.sensorRoom}
            cardIcon={cardData.cardIcon}
            cardColour={cardData.cardColour}
            sensorData={cardData.sensorData}
            setSelectedCardForQuickUpdate={props.setSelectedCardForQuickUpdate}
            loadingCardModalView={props.loadingCardModalView}
            setLoadingCardModalView={props.setLoadingCardModalView}
        />
    )
}