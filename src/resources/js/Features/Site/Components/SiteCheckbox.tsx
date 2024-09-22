import { Checkbox, HStack, Image, Text } from '@chakra-ui/react';
import { ComponentProps } from 'react';
import { Site } from '../Types';

type Props = {
  site: Site;
} & ComponentProps<typeof Checkbox>;

export function SiteCheckBox({ site, ...props }: Props) {
  return (
    <Checkbox variant="highlight" value={`${site.id}`} {...props}>
      {site.logo ? (
        <HStack>
          <Image
            src={site?.logo?.original_url}
            alt=""
            h="100%"
            w="100%"
            borderRadius="100%"
            boxSize={6}
            objectFit="cover"
          />
          <Text as="span">{site.name}</Text>
        </HStack>
      ) : (
        <Text as="span">{site.name}</Text>
      )}
    </Checkbox>
  );
}
