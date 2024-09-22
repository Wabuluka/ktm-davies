import { usePage } from '@inertiajs/react';
import { User } from '../Types';

type PageProps = { auth: { user: User } };

export const useCurrentUser = () => {
  return usePage<PageProps>().props.auth.user;
};
