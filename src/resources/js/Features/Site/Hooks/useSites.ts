import { usePage } from '@inertiajs/react';
import { Site } from '../Types';

type PageProps = {
  master: { sites: Site[] };
};

export function useSites() {
  return usePage<PageProps>().props.master.sites;
}
