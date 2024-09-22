import {
  Link as ChakraLink,
  LinkOverlay as ChakraLinkOverlay,
  LinkOverlayProps,
  LinkProps,
} from '@chakra-ui/react';
import { Link as InertiaLink, InertiaLinkProps } from '@inertiajs/react';
import { FC } from 'react';

// prettier-ignore
type Props =
  | (Omit<InertiaLinkProps, 'size'> & LinkProps & { overlay?: false })
  | (Omit<InertiaLinkProps, 'size'> & LinkOverlayProps & { overlay: true });

export const Link: FC<Props> = ({
  children,
  isExternal,
  overlay,
  ...props
}) => {
  const as = isExternal ? 'a' : AccessibleInertiaLink;

  return overlay ? (
    <ChakraLinkOverlay as={as} isExternal={isExternal} {...props}>
      {children}
    </ChakraLinkOverlay>
  ) : (
    <ChakraLink as={as} isExternal={isExternal} {...props}>
      {children}
    </ChakraLink>
  );
};

const AccessibleInertiaLink: FC<InertiaLinkProps> = ({
  children,
  method,
  ...props
}) => {
  /*
    Avoid a warning.
    see: https://github.com/inertiajs/inertia/blob/fe35815bfc83acde823027130b31350019671a94/packages/react/src/Link.js#L87
  */
  const as = ['post', 'put', 'patch', 'delete'].some(
    (m) => m === method?.toLowerCase(),
  )
    ? 'button'
    : 'a';

  return (
    <InertiaLink as={as} method={method} {...props}>
      {children}
    </InertiaLink>
  );
};
