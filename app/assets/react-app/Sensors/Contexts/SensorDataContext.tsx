import { createContext } from "react";
import { SensorDataContextDataInterface } from "../DataProviders/SensorDataProvider";

const SensorDataContext = createContext<SensorDataContextDataInterface | null>(null);

export default SensorDataContext;
