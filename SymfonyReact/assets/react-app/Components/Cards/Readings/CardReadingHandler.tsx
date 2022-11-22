import * as React from 'react';
import { useState, useEffect } from 'react';

import axios from 'axios';
import { baseCardDataURL } from '../../../Common/CommonURLs';
import { AxiosResponse } from 'axios';

import { handleSendingCardDataRequest } from '../../../Request/CardRequest';

import { CardDataResponseInterface } from '../../../Response/User/CardData/Interfaces/CardDataResponseInterface';

import CardReadingFactory from '../../../Factories/CardReadingFactory';
import { BaseCard } from '../BaseCard';
import { CardRowContainer } from '../CardRowContainer';
import { CardSensorReadings } from './CardSensorReadings';

export function CardReadingHandler(props) {
    const route:string = props.route ?? 'index';
    const [cardDataUserResponse, setCardDataUserResponse] = useState<Array<CardDataResponseInterface>>([]);
    const [refreshTimer, setRefreshTimer] = useState<number>(3000);
    const [cardsForDisplay, setCardsForDisplay] = useState<Array<CardDataResponseInterface>>([]);

    useEffect(() => {
        const interval = setInterval(() => {
            handleCardRefresh();
        }, refreshTimer);
        
        return () => clearInterval(interval);
    }, []);
    
    const handleCardRefresh = async () => {
        const cardData: CardDataResponseInterface = await handleGettingSensorReadings(route);
        const cardsForDisplay = prepareCardDataForDisplay(cardData);


        setCardDataUserResponse(cardsForDisplay);
      }


    const handleGettingSensorReadings = async (route: string): Promise<CardDataResponseInterface> => {
        const cardDataResponse: AxiosResponse = await handleSendingCardDataRequest({route});
        const cardData: CardDataResponseInterface = cardDataResponse.data.payload;

        return cardData;
    }


    const prepareCardDataForDisplay = (cardData: CardDataResponseInterface) => {
        // call a builder to check the returned card types and build the card data from there
        // cardData.sensorData.map((value, index, array) => {
            
            // })
    }


    return (
        <React.Fragment>
            <CardRowContainer content={
                <BaseCard content={
                    <CardSensorReadings 
                        content={<h1>hi</h1>}
                    />
                } />
            } />
        </React.Fragment>
    );
}



//             <div className="row">
//  <div className="col-xl-3 col-md-6 mb-4" onClick={() => {}} key={1}>
//  <div className={"shadow h-100 py-2 card border-left-primary ADD-COLOUR-HERE"}>
//    <div className="card-body hover">
//      <div style={{ position: "absolute", top: '2%', right: '5%'}}>SensorType</div>
//      <div className="row no-gutters align-items-center">
//        <div className="col mr-2">
//          <div className="d-flex font-weight-bold text text-uppercase mb-1">Sensor Name:</div>
//          <div className="d-flex text text-uppercase mb-1">Room: </div>
//          {/* {
// //           cardData.sensorData.length >= 1 
// //             ? cardData.sensorData.map((sensorData, index) => (
// //               <React.Fragment key={index}>
// //                   {context.modalLoading !== false && context.modalLoading === cardData.cardViewID ? <div style={{zIndex:"1"}} className="absolute-center fa-4x fas fa-spinner fa-spin"/> : null}
// //                   <div className={'card-font mb-0 font-weight-bold '+senorReadingStyle(sensorData.highReading, sensorData.lowReading, sensorData.currentReading)}>
// //                     {capitalizeFirstLetter(sensorData.readingType)}: {sensorData.currentReading}{sensorData.readingSymbol}
// //                   </div>
// //                   <div className="card-font mb-0 text-gray-400">updated@{sensorData.updatedAt}</div>
// //                 </React.Fragment>
// //               ))
// //             : <p>No Sensor Data</p>
// //         } */}
//        </div>
//        <div className="col-auto">
//          <i className={"fas fa-2x text-gray-300 fa-microchip"}></i>
//        </div>
//      </div>
//    </div>
//  </div>
//  </div> 
//  </div>  