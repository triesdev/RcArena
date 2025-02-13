import {OhVueIcon, addIcons} from "oh-vue-icons";
import {
    BiArchive,
    BiArrowLeft,
    BiBezier2,
    BiBookmarkStar,
    BiCameraVideo,
    BiCash,
    BiCheck2All,
    BiColumnsGap,
    BiCursor,
    BiCaretDown,
    BiCaretUp,
    BiEye,
    BiExclamationCircleFill,
    BiFilePost,
    BiFileEarmarkPost,
    BiGear,
    BiListCheck,
    BiMenuButtonWide,
    BiNewspaper,
    BiPeople,
    BiPlus,
    BiSearch,
    BiShieldCheck,
    BiSave,
    BiTags,
    BiTicketDetailed,
    BiTrash,
    BiVectorPen,
    BiWhatsapp, BiPen,
} from "oh-vue-icons/icons/bi";

addIcons(
    BiArchive,
    BiArrowLeft,
    BiBezier2,
    BiBookmarkStar,
    BiCameraVideo,
    BiCash,
    BiCheck2All,
    BiColumnsGap,
    BiCursor,
    BiCaretDown,
    BiCaretUp,
    BiEye,
    BiExclamationCircleFill,
    BiFilePost,
    BiFileEarmarkPost,
    BiGear,
    BiListCheck,
    BiMenuButtonWide,
    BiNewspaper,
    BiPeople,
    BiPlus,
    BiSearch,
    BiShieldCheck,
    BiSave,
    BiTags,
    BiTicketDetailed,
    BiTrash,
    BiVectorPen,
    BiWhatsapp,
    BiPen
);

// register components
const registerIcon = (app) => {
    app.component('v-icon', OhVueIcon);
}

export default registerIcon
