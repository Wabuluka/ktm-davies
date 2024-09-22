import { Image, ImageProps } from '@chakra-ui/react';
import logo from '/resources/images/logo.png';
import logoTransparent from '/resources/images/logo_transparent.png';

type Props = ImageProps & {
  transparent?: boolean;
};

export default function ApplicationLogo({
  transparent = false,
  ...props
}: Props) {
  const src = transparent ? logoTransparent : logo;

  return <Image src={src} {...props} />;
}
