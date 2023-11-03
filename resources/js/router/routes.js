import auth from "../middleware/auth";
import checkAuth from "../middleware/auth-check";
import general from "./routeChild/general";
import hr from "./routeChild/hr";
import realEstate from "./routeChild/realEstate";
import archiving from "./routeChild/archiving";
import recievablePayable from "./routeChild/recievablePayable";
import boardRent from "./routeChild/boardRent";
import club from "./routeChild/club";
import pointOfSell from "./routeChild/pointOfSell";
import booking from "./routeChild/booking";

export default [
    ...club,
    ...hr,
    ...realEstate,
    ...archiving,
    ...recievablePayable,
    ...boardRent,
    ...pointOfSell,
    ...booking,
    ...general,
]
