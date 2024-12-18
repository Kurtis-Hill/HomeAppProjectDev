import * as React from 'react';

import { useState, useEffect } from 'react';
import { AnnouncementFlashModalBuilder } from '../../../Common/Builders/ModalBuilder/AnnouncementFlashModalBuilder';
import SubmitButton from '../../../Common/Components/Buttons/SubmitButton';
import { Label } from '../../../Common/Components/Elements/Label';
import { AnnouncementFlashModal } from '../../../Common/Components/Modals/AnnouncementFlashModal';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import { capitalizeFirstLetter } from '../../../Common/StringFormatter';
import { ColourResponseInterface } from '../../Response/Colour/ColourResponseInterface';
import { IconResponseInterface } from '../../Response/Icons/IconResponseInterface';
import StateResponseInterface from '../../Response/State/StateResponseInterface';
import { StandardCardFormResponseInterface } from '../../Response/Cards/CardForms/StandardCardFormResponseInterface';
import { CardUpdateRequestInterface, updateCardRequest } from '../../Request/Cards/Card/CardUpdateRequest';
import { getCardViewFormRequest } from '../../Request/Cards/Card/GetCardViewFormRequest';

export function UpdateCard(props: {cardViewID: number}) {
    const { cardViewID } = props;

    const [announcementModals, setAnnouncementModals] = useState<React.JSX.Element[]>([]);

    const [updateCardRequestLoading, setUpdateCardRequestLoading] = useState<boolean>(false);
    
    const [cardViewUserForm, setCardViewUserForm] = useState<StandardCardFormResponseInterface|null>(null);

    const showAnnouncementFlash = (message: Array<string>, title: string, timer?: number | null): void => {
        setAnnouncementModals([
            <AnnouncementFlashModalBuilder
                setAnnouncementModals={setAnnouncementModals}
                title={title}
                dataToList={message}
                timer={timer ? timer : 40}
            />
        ])
    }

    const handleCardViewFormRequest = async () => {
        const cardViewUserFormResponse = await getCardViewFormRequest(cardViewID);

        const cardViewUserFormResponseData: StandardCardFormResponseInterface = cardViewUserFormResponse.data.payload;

        if (cardViewUserFormResponse.status === 200) {
            setCardViewUserForm(cardViewUserFormResponseData);
        }
    }

    useEffect(() => {
        handleCardViewFormRequest();
    }, [cardViewID])

    const updateCardFormInput = (e: React.ChangeEvent<HTMLSelectElement>): void => {
        const target = e.target as HTMLInputElement;
        const name = target.name;
        const value = target.value;
        
        switch (name) {
            case 'card-icon':
                for (let i = 0; i < cardViewUserForm.cardUserSelectionOptions.icons.length; i++) {
                    if (cardViewUserForm.cardUserSelectionOptions.icons[i].iconID === parseInt(value)) {
                        setCardViewUserForm((prevState: StandardCardFormResponseInterface) => ({
                            ...prevState,
                            currentCardIcon: {
                                description: cardViewUserForm.cardUserSelectionOptions.icons[i].description,
                                iconName: cardViewUserForm.cardUserSelectionOptions.icons[i].iconName,
                                iconID: cardViewUserForm.cardUserSelectionOptions.icons[i].iconID,
                            }
                        })) 
                    }
                }
                break;
            case 'card-colour':
                for (let i = 0; i < cardViewUserForm.cardUserSelectionOptions.colours.length; i++) {
                    if (cardViewUserForm.cardUserSelectionOptions.colours[i].colourID === parseInt(value)) {
                        setCardViewUserForm((prevState: StandardCardFormResponseInterface) => ({
                            ...prevState,
                            currentCardColour: {
                                shade: cardViewUserForm.cardUserSelectionOptions.colours[i].shade,
                                colour: cardViewUserForm.cardUserSelectionOptions.colours[i].colour,
                                colourID: cardViewUserForm.cardUserSelectionOptions.colours[i].colourID,
                            }
                        }))
                    }
                }
                break;
            case 'card-view-state':
                for (let i = 0; i < cardViewUserForm.cardUserSelectionOptions.states.length; i++) {
                    if (cardViewUserForm.cardUserSelectionOptions.states[i].cardStateID === parseInt(value)) {
                        setCardViewUserForm((prevState: StandardCardFormResponseInterface) => ({
                            ...prevState,
                            currentViewState: {
                                cardState: cardViewUserForm.cardUserSelectionOptions.states[i].cardState,
                                cardStateID: cardViewUserForm.cardUserSelectionOptions.states[i].cardStateID,
                            }
                        }))
                    }
                }
                break;
                default:
                    setCardViewUserForm({
                        ...cardViewUserForm,
                        [name]: value
                })
                break;
        } 
    }

    const handleUpdateCardRequest = async (e: Event) => {
        e.preventDefault();
        setUpdateCardRequestLoading(true);

        const cardUpdateRequest: CardUpdateRequestInterface = {
            cardColour: cardViewUserForm.currentCardColour.colourID,
            cardIcon: cardViewUserForm.currentCardIcon.iconID,
            cardViewState: cardViewUserForm.currentViewState.cardStateID,
        }

        const cardUpdateResponse = await updateCardRequest(cardViewID, cardUpdateRequest);
        
        cardUpdateResponse.status !== null ? setUpdateCardRequestLoading(false) : null
        
        if (cardUpdateResponse.status === 200) {
            showAnnouncementFlash([`Card updated`], `${cardUpdateResponse.data.title}`, 15);
        } 
    }

    if (cardViewUserForm === null) {
        return (
            <DotCircleSpinner spinnerSize={3} classes="center-spinner" />
        )
    }
    
    return (
        <>
            {
                announcementModals.map((announcementModal: typeof AnnouncementFlashModal, index: number) => {
                    return (
                        <React.Fragment key={index}>
                            { announcementModal }
                        </React.Fragment>
                    );
                })
            }
            <form>
                <Label 
                    text="Colour Selection"
                    htmlFor='card-colour'
                />
                <select name="card-colour" value={cardViewUserForm.currentCardColour.colourID} onChange={(e: React.ChangeEvent<HTMLSelectElement>) => {updateCardFormInput(e)}} className="form-control form-bottom-margin">
                    {cardViewUserForm.cardUserSelectionOptions.colours.map((colour: ColourResponseInterface) => (
                    <option value={colour.colourID} key={colour.colourID}>{capitalizeFirstLetter(colour.colour)}</option>
                    ))}
                </select>

                <Label 
                    text="Card State Selection"
                    htmlFor='card-view-state'
                />
                <select name="card-view-state" value={cardViewUserForm.currentViewState.cardStateID} onChange={(e: React.ChangeEvent<HTMLSelectElement>) => {updateCardFormInput(e)}} className="form-control form-bottom-margin">
                    {cardViewUserForm.cardUserSelectionOptions.states.map((states: StateResponseInterface) => (
                    <option value={states.cardStateID} key={states.cardStateID}>{capitalizeFirstLetter(states.cardState)}</option>
                    ))}
                </select>

                <Label 
                    text="Icon Selection"
                    htmlFor='card-icon'
                />
                <br />
                <select name="card-icon" id="icon-select" value={cardViewUserForm.currentCardIcon.iconID} onChange={(e: React.ChangeEvent<HTMLSelectElement>) => {updateCardFormInput(e)}} className="form-space-left form-bottom-margin">
                    {cardViewUserForm.cardUserSelectionOptions.icons.map((icon: IconResponseInterface) => (
                        <option key={icon.iconID} value={icon.iconID}>{capitalizeFirstLetter(icon.iconName)}</option>
                    ))}
                </select>
                <i className={`fas fa-2x text-gray-300 modal-icon fa-${cardViewUserForm.currentCardIcon.iconName}`}></i>
                <br />

                {
                    updateCardRequestLoading === true
                        ?
                            <DotCircleSpinner  classes="center-absolute-center" />
                        :
                            <SubmitButton
                                type="submit"
                                text='Update Card'
                                name='update-card'
                                action='submit'
                                classes='update-card'
                                onClickFunction={(e) => handleUpdateCardRequest(e)}
                            />
                }
            </form>

        </>
    )
}
