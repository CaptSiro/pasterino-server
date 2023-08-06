<?php

  const SESSION_EXPIRATION_TIME = 30 * 24 * 60 * 60;

  const SESSION_EXPIRATION_TIME_MS = SESSION_EXPIRATION_TIME * 1_000;

  const SESSION_MAX_TRIES = 10_000;
  
  const SESSION_COOKIE = "pasterino_session";
  
  const ENV = __DIR__ . "/.env";