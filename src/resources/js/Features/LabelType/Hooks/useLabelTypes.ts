import { usePage } from '@inertiajs/react';
import { LabelType } from '../Types';

type PageProps = {
  master: { labelTypes: LabelType[] };
};

export function useLabelTypes() {
  return usePage<PageProps>().props.master.labelTypes;
}
