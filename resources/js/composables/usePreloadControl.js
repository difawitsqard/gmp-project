import { skipNextPreload } from "@/stores/preload";

export function skipPreloadNextRequest() {
    skipNextPreload.value = true;
}
