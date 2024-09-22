import { Link } from '@/UI/Components/Navigation/Link';
import {
  HStack,
  List,
  ListItem,
  Popover,
  PopoverArrow,
  PopoverBody,
  PopoverCloseButton,
  PopoverContent,
  PopoverHeader,
  PopoverTrigger,
  Square,
  Text,
} from '@chakra-ui/react';
import { FC } from 'react';
import { BsBoxArrowInLeft, BsPerson } from 'react-icons/bs';
import { useCurrentUser } from '../Hooks/useCurrentUser';
import { UserAvatar } from './UserAvatar';

const ProfileButton: FC = () => {
  const user = useCurrentUser();

  return (
    <Popover placement="bottom">
      <PopoverTrigger>
        <Square as="button" size={20}>
          <UserAvatar username={user.name} />
        </Square>
      </PopoverTrigger>

      <PopoverContent
        color="white"
        fontSize="lg"
        bg="gray.700"
        borderColor="gray.700"
      >
        <PopoverHeader pt={4} fontWeight="bold" border="0">
          Logged in as{' '}
          <Text as="span" color="pink.500" fontSize="xl">
            {user.name}
          </Text>
        </PopoverHeader>

        <PopoverArrow bg="gray.700" />

        <PopoverCloseButton />

        <PopoverBody>
          <List spacing={4} pb={4}>
            <ListItem>
              <Link href={route('profile.edit')}>
                <HStack>
                  <BsPerson />
                  <Text>Your profile</Text>
                </HStack>
              </Link>
            </ListItem>
            <ListItem>
              <Link method="post" href={route('logout')} w="100%">
                <HStack>
                  <BsBoxArrowInLeft />
                  <Text>Logout</Text>
                </HStack>
              </Link>
            </ListItem>
          </List>
        </PopoverBody>
      </PopoverContent>
    </Popover>
  );
};

export default ProfileButton;
