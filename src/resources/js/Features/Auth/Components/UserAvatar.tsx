import { Avatar, AvatarProps } from '@chakra-ui/react';
import React from 'react';

type Props = AvatarProps & {
  username: string;
};

export const UserAvatar = React.forwardRef<HTMLSpanElement, Props>(
  function UserAvatarBase({ username, ...props }, ref) {
    return <Avatar name={username} ref={ref} {...props} />;
  },
);
